# Image Styling Notes

Include images inside `div` blocks with certain classes to control how they display.

## Default

By default images occupy the full width and are left-aligned

```
TODO: ![](small.jpg)
```

![](small.jpg)

## Center

Use bootstrap's `text-center`

```
<div class="text-center">

TODO: ![](small.jpg)

</div>
```

<div class="text-center">

![](small.jpg)

</div>

## Border and Shadow

These two stylings come together with the `img-border` class


```
<div class="text-center img-border">

TODO: ![](small.jpg)

</div>
```

<div class="text-center img-border">

![](small.jpg)

</div>

## Sizing

Size of large images can be constrained with the following classes:

* `img-micro`
* `img-small`
* `img-medium`

### Micro

<div class="text-center img-border img-micro">

![](large.jpg)
![](large.jpg)
![](large.jpg)

</div>

### Small

<div class="text-center img-border img-small">

![](large.jpg)
![](large.jpg)

</div>

### Medium

<div class="text-center img-border img-medium">

![](large.jpg)

</div>

### No Restriction

<div class="text-center img-border">

![](large.jpg)

</div>

### Remote Image

<div class="text-center img-border">

![](https://mods.org/wp-content/uploads/2017/02/test-image.png)

</div>