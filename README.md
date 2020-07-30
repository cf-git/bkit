# bkit
Laravel Blade Kit

## Tool list
1. pushOnce/endOnce
2. set/endSet

### pushOnce/endOnce
*app/layout.blade.php*
```blade
{{-- ... --}}
@stack('scripts')
{{-- ... --}}
```
*any.blade.php*
```blade
@foreach(range(1,3) as $i)
    @pushOnce('scripts', 'any-key-to-unification-of-push')
        <script>alert($i)</script>
    @endOnce
@endforeach
```
Will display only 1 alert with first **$i** value

### set/endSet
Set like in twig
*some.blade.php*
```blade
@set('myNewVariable')
    <p>Some content</p>
@endSet
<h1>set/endSet demonstration</h1>
{!! $myNewVaiable !!}

@set('myNewVariable')
    {!! $myNewVaiable !!}
    <p>This text appended to previous value</p>
@endSet

@set('myNewVariable')
    <p>This text replace value</p>
@endSet
```