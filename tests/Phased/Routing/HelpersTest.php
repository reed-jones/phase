<?php

it("registers the 'array_merge_phase' merge strategy", function () {
    assertSame(array_merge_phase([]), []);
});

it("merges arbitrary number of arguments", function () {
    assertSame(
        array_merge_phase(['a' => 1], ['b' => 2], ['c' => 3], ['d' => 4]),
        ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]
    );
});

it('merges deeply nested array', function () {
    $a = ['a' => ['b' => ['c' => ['d' => ['e' => 1, 'f' => 2, 'g' => 3]]]]];
    $b = ['a' => ['b' => ['c' => ['d' => ['h' => 4, 'i' => 5, 'g' => 6]]]]];
    $merged = array_merge_phase($a, $b);
    $outcome = ['a' => ['b' => ['c' => ['d' => ['e' => 1, 'f' => 2, 'g' => 6, 'h' => 4, 'i' => 5]]]]];
    assertSame($merged, $outcome);
});

it('takes the last value based on keys', function () {
    $merged = array_merge_phase(
        ['a' => 1],
        ['a' => 2],
        ['a' => 3],
        ['a' => 4],
        ['a' => 5],
    );

    assertSame($merged, ['a' => 5]);
});
