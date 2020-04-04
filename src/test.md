# md2html Tests

This markdown file contains several examples of advanced features

> ⚠️ **WARNING:** This project is early in development

## Images

![](https://www.gstatic.com/webp/gallery/4.jpg)

## Table of Contents

Only headings below the table of contents are included in the list.

![](TOC)

## Links nested in ()

* test [a](#testA) and [b](#testB) and [c](#testC) and [d](#testD) work
* test ([a](#testA)) and [b](#testB) and ([c](#testC)) and [d](#testD) work

## Allow HTML in paragraphs

This should be <b>bold just fine</b> no issue.

<p align="center">V<sub>cell</sub> = V<sub>measured</sub> - LJP</p>

## Links with `)` in their URL

**The [Einstein relation](https://en.wikipedia.org/wiki/Einstein_relation_(kinetic_theory))** defines diffusion as **`D = Âµ * k * T`** where...

## Nested formatters
* **`D`** is the diffusion coefficient 
* **`µ`** (mu) is [ionic mobility](https://en.wikipedia.org/wiki/Electrical_mobility) value

## Nested Lists

* level 1
  * level 2
    * level 3
      * level 4
        * level 5
          * level 6
          * level 6
        * level 5
      * level 4
    * level 3
  * level 2
* level 1

# Heading 1
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6

## Backslash ignore character

* Ignore this \*\*bold\*\*
* Ignore this \*italic\*
* Ignore this \_italic\_
* Ignore this \~~strikeout\~~
* Ignore this \`code\`
* Ignore this [link]\(http://www.google.com)

## Formatting in bullets

* this should display **bold** text and [links](http://www.google.com) too.

## Formatting in headers

#### this should display ~~strike~~ and _italic_ text and [links](http://www.google.com) too.

## Including HTML inside code blocks

```html
<!-- can you see this? -->
<br id="or this?">
```

## Syntax Highlighting

### Auomatic

```auto
public void test(){
  System.Console.WriteLine("test");
}
```


### Defined Language

correct:
```python
print("test")
```

incorrect:
```xml
print("test")
```

### Off

```
Values for cL were adjusted to achieve electro-neutrality:

 Name               | Charge | Conductivity (E-4) | C0 (mM)      | CL (mM)      
--------------------|--------|--------------------|--------------|--------------
 K                  | +1     | 73.5               | 145          | 2.8098265   
 Na                 | +1     | 50.11              | 13           | 144.9794365 
 Mg                 | +2     | 53.06              | 1            | 1.9998212   
 Ca                 | +2     | 59.5               | 0            | 0.9999109   
 HEPES              | -1     | 22.05              | 5            | 4.9990023   
 Gluconate          | -1     | 24.255             | 145          | 0           
 Cl                 | -1     | 76.31              | 10           | 148.789725

Equations were solved in 88.91 ms
LJP at 20 C (293.15 K) = 16.052319631180264 mV
```

### Real Tables

heading A|heading B|heading C
---|---|---
***lolz***|lolz|lolz
lolz|**lolz**|lolz
lolz|lolz|*lolz*