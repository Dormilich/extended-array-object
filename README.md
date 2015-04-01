# extended-array-object
PHP’s array functions brought into php objects.

The intention of this project is to extend the native `ArrayObject` with further array functions and 
provide an easy way to apply subsequent array operations (like filter, map, diff, intersect) in a 
concise way.

So far the spotlight is on working with single dimensional arrays. Recursive functions may be included 
in another project.

## Function Overview

the following functions are supported:

### Functions with identical behaviour and syntax

- `array_change_key_case()` as `changeKeyCase()`
- `in_array()` as `contains()`
- `array_count_values()` as `countValues()`
- `array_filter()` as `filter()`
- `array_flip()` as `flip()`
- `implode()` as `join()`
- `array_keys()` as `keys()`
- `array_pop()` as `pop()`
- `array_push()` as `push()`
- `array_reverse()` as `reverse()`
- `array_search()` as `search()`
- `array_shift()` as `shift()`
- `shuffle()`
- `array_slice()` as `slice()`
- `array_splice()` as `splice()`
- `array_unique()` as `unique()`
- `array_unshift()` as `unshift()`
- `array_values()` as `values()`

Some methods have been renamed to better describe their functionality

- `array_merge()` as `concat()`
- `array_replace()` as `merge()`

### Functions with slightly modified behaviour or syntax

- `array_map()` as `map()`, it does no accept multiple arrays. Instead it will always pass 
the keys and values to the callback.
- `array_rand()` as `rand()`, it returns the array elements instead of only the keys.
- `array_reduce()` as `reduce()`, if no initial value is given, the first array element is used
as initial value.
- `*sort()`, the native sort methods (ksort/asort) are extended to return the object instance.
- `array_walk()` as `walk()`, if no userdata are given, the array itself is injected as userdata.

### Functions with drastically changed behaviour or syntax
- `array_diff_*()` as `diff()`, `kdiff()`, and `adiff()`
- `array_intersect_*()` as `intersect()`, `kintersect()`, and `aintersect()`
- `replace()` is a new function that works like `array_replace()` only without appending excess data.

## Improved Error Handling

All methods throw exceptions when they encounter an error. This effectively allows to chain the methods together.

## Examples

* Filtering for duplicate data in a POST variable
```
use Dormilich\Core\ArrayObject as XArray;

try {
    $group = new XArray($_POST['group']);
    $error_string = 'Duplicate names: ' . $group->map(function ($item) {
        return $item['name'];
    })
    ->countValues()
    ->filter(function ($count) {
        return $count > 1;
    })
    ->map(function ($count, $name) {
        return sprintf('%d× %s', $count, $name);
    })
    ->join(', ');
}
catch (Exception $exc) {
    $error_string = $exc->getMessage();
}
```
