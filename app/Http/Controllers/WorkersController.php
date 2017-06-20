<?php

namespace App\Http\Controllers;

use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class WorkersController extends Controller
{
    protected $worker;

    protected $quantityPerPage = 10;

    protected $alphabeticSectionsQuantity = 7;

    const COST = 0;
    const DELIMITER = 1;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    public function index(Request $request)
    {
        $department = $request->get('department');
        $working = $request->exists('working') ? $request->get('working') : null;

        $builder = $this->worker->query();
        if ($department) {
            $builder->where('department_id', $department);
        }
        if (isset($working) && ($working == 'true' || $working == 'false')) {
            $builder->{$working == 'true' ? 'whereNotNull' : 'whereNull'}('work_end');
        }
        $workers = $builder->paginate($this->quantityPerPage);

        return view('admin.workers.index', [
            'departments' => \App\Department::all(),
            'workers' => $workers->appends(Input::except('page'))
        ]);
    }

    public function detail($id)
    {
        $worker = $this->worker->find($id);
        $data = $worker->toArray();
        unset($data['department_id']);
        $data['department'] = $worker->department->title;

        return view('admin.workers.detail', [
            'data' => $data
        ]);
    }

    public function alphabetically($sectionChosen = 1)
    {
//        setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
        $workers = $this->worker->with('department')->orderBy('surname', 'asc')->get();
        $workersArrays = $workers->toArray();
//        sort($workersSurnames, SORT_LOCALE_STRING);

        $mappedToFirstLetter = array_reduce(
            $workersArrays,
            function ($mappedToFirstLetter, $worker) {
                $letter = mb_strtoupper(mb_substr($worker['surname'], 0, 1));

                if (!isset($mappedToFirstLetter[$letter])) {
                    $mappedToFirstLetter[$letter] = [];
                }

                $mappedToFirstLetter[$letter][] = $worker;

                return $mappedToFirstLetter;
            },
            []
        );
        $countByLetters = array_values(array_map('count', $mappedToFirstLetter));
        $sectionsList = $this->linear_partition_second_implementation($countByLetters, $this->alphabeticSectionsQuantity);

        $chunked = array_reduce(
            $sectionsList,
            function ($chunked, $section) use (&$mappedToFirstLetter) {
                $chunk = array_reduce(range(1, count($section)), function ($chunk) use (&$mappedToFirstLetter) {
                    $chunk[key($mappedToFirstLetter)] = array_shift($mappedToFirstLetter);
                    return $chunk;
                }, []);

                $keys = array_keys($chunk);
                switch (count($keys)) {
                    case 1:
                        $key = $keys[0];
                        break;
                    default:
                        $key = $keys[0] . ' - ' . $keys[count($keys) - 1];
                }
                if (empty($chunked[$key])) {
                    $chunked[$key] = [];
                }
                foreach ($chunk as $c) {
                    $chunked[$key] = array_merge($chunked[$key], $c);
                }

                return $chunked;
            },
            []
        );
        return view('admin.workers.alphabetically', [
            'workers' => $chunked[array_keys($chunked)[$sectionChosen - 1]],
            'groups' => array_keys($chunked),
        ]);
    }

    protected function linear_partition_first_implementation(array $seq, $k)
    {
        if ($k <= 0) {
            return array();
        }

        $n = count($seq) - 1;
        if ($k > $n) {
            return array_map(function ($x) {
                return array($x);
            }, $seq);
        }

        list($table, $solution) = $this->linear_partition_table($seq, $k);
        $k = $k - 2;
        $ans = array();

        while ($k >= 0) {
            $ans = array_merge(array(array_slice($seq, $solution[$n - 1][$k] + 1, $n - $solution[$n - 1][$k])), $ans);
            $n = $solution[$n - 1][$k];
            $k = $k - 1;
        }

        return array_merge(array(array_slice($seq, 0, $n + 1)), $ans);
    }

    protected function linear_partition_first_implementation_table($seq, $k)
    {
        $n = count($seq);

        $table = array_fill(0, $n, array_fill(0, $k, 0));
        $solution = array_fill(0, $n - 1, array_fill(0, $k - 1, 0));

        for ($i = 0; $i < $n; $i++) {
            $table[$i][0] = $seq[$i] + ($i ? $table[$i - 1][0] : 0);
        }

        for ($j = 0; $j < $k; $j++) {
            $table[0][$j] = $seq[0];
        }

        for ($i = 1; $i < $n; $i++) {
            for ($j = 1; $j < $k; $j++) {
                $current_min = null;
                $minx = PHP_INT_MAX;

                for ($x = 0; $x < $i; $x++) {
                    $cost = max($table[$x][$j - 1], $table[$i][0] - $table[$x][0]);
                    if ($current_min === null || $cost < $current_min) {
                        $current_min = $cost;
                        $minx = $x;
                    }
                }

                $table[$i][$j] = $current_min;
                $solution[$i - 1][$j - 1] = $minx;
            }
        }

        return array($table, $solution);
    }


    public function linear_partition_second_implementation(array $elements, int $max_ranges)
    {
        // 0) Validate input
        if ($max_ranges < 0) {
            throw new \InvalidArgumentException("\$max_ranges should be a non-negative integer. {$max_ranges} given.");
        }
        foreach ($elements as $i => $element) {
            if ( ! is_int($element) && ! is_float($element)) {
                $type = is_object($element) ? get_class($element) : gettype($element);
                throw new \InvalidArgumentException("\$elements should be an array of positive numbers. Element #$i is of type $type.");
            }
            if ($element <= 0) {
                throw new \InvalidArgumentException("\$elements should be an array of positive numbers. Element #$i is $element.");
            }
        }
        // An array S of non-negative numbers {s1, ... ,sn}
        $s = array_merge([null], array_values($elements)); // adapt indices here: [0..n-1] => [1..n]
        // Integer K - number of ranges to split items into
        $k = $max_ranges;
        $n = count($elements);
        // Let M[n,k] be the minimum possible cost over all partitionings of N elements to K ranges
        $m = [];
        // Let D[n,k] be the position of K-th divider
        // which produces the minimum possible cost partitioning of N elements to K ranges
        $d = [];
        // Note: For code simplicity we don't use zero indices for `m` and `d`
        //       to make code match math formulas
        // Let pi be the sum of first i elements (cost calculation optimization)
        $p = [];
        // 1) Init prefix sums array
        //    pi = sum of {s1, ..., si}
        $p[0] = 0;
        for ($i = 1; $i <= $n; $i++) {
            $p[$i] = $p[$i - 1] + $s[$i];
        }
        // 2) Init boundaries
        for ($i = 1; $i <= $n; $i++) {
            // The only possible partitioning of i elements to 1 range is a single all-elements range
            // The cost of that partitioning is the sum of those i elements
            $m[$i][1] = $p[$i]; // sum of {s1, ..., si} -- optimized using pi
        }
        for ($j = 1; $j <= $k; $j++) {
            // The only possible partitioning of 1 element into j ranges is a single one-element range
            // The cost of that partitioning is the value of first element
            $m[1][$j] = $s[1];
        }
        // 3) Main recurrence (fill the rest of values in table M)
        for ($i = 2; $i <= $n; $i++) {
            for ($j = 2; $j <= $k; $j++) {
                $solutions = [];
                for ($x = 1; $x <= ($i - 1); $x++) {
                    $solutions[] = [
                        self::COST      => max($m[$x][$j - 1], $p[$i] - $p[$x]),
                        self::DELIMITER => $x,
                    ];
                }
                usort($solutions, function (array $x, array $y) {
                    return ($x[self::COST] < $y[self::COST]) ? -1 : (($x[self::COST] > $y[self::COST]) ? 1 : 0);
                });
                $best_solution = $solutions[0];
                $m[$i][$j] = $best_solution[self::COST];
                $d[$i][$j] = $best_solution[self::DELIMITER];
            }
        }
        // 4) Reconstruct partitioning
        $i = $n;
        $j = $k;
        $partition = [];
        while ($j > 0) {
            // delimiter position
            $dp = isset($d[$i][$j]) ? $d[$i][$j] : 0;
            // Add elements after delimiter {sdp, ..., si} to resulting $partition.
            $partition[] = array_slice($s, $dp + 1, $i - $dp);
            // Step forward: look for delimiter position for partitioning M[$dp, $j-1]
            $i = $dp;
            $j = $j - 1;
        }
        // Fix order as we reconstructed the partitioning from end to start
        return array_reverse($partition);
    }
}
