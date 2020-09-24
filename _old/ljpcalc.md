# LJPcalc

LJPcalc is a free and open source liquid junction potential (LJP) calculator. 

```php
// demo with defined PHP highlighting
$lines = explode("\n", file_get_contents($filePath));
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) == "![](TOC)") {
        $lines[$i] = getTOC($lines);
    }
}
```

```xml
// demo with defined xml
$lines = explode("\n", file_get_contents($filePath));
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) == "![](TOC)") {
        $lines[$i] = getTOC($lines);
    }
}
```

```
// demo with no defined language
$lines = explode("\n", file_get_contents($filePath));
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) == "![](TOC)") {
        $lines[$i] = getTOC($lines);
    }
}
```

LJPcalc is available as a simple click-to-run EXE for Windows and a console application for Linux and MacOS.

<div align="center">

![](https://raw.githubusercontent.com/swharden/LJPcalc/master/src/LJPcalc/screenshot.png)

</div>

## Download 

  * Windows (64-bit): [LJPcalc.zip](releases) is provided for each [release](releases)
  * Linux and MacOS: [LJPconsole](src/LJPconsole)

## Theory

### Calculation Method

LJPcalc calculates the liquid junction potential according to the stationary Nernst-Planck equation which is typically regarded as superior to the simpler Henderson equation used by most commercial LJP calculators. Both equations produce nearly identical LJPs, but the Henderson equation becomes inaccurate as ion concentrations increase, and also when calculating LJP for solutions containing polyvalent ions.

### Ion Mobility Library:
Ion charges and conductivities are stored in [IonTable.md](/src/IonTable.md) which is easy to view and modify as needed.

### Ion sequence

> ℹ️ LJPcalc automatically sorts the ion table into an ideal sequence prior to solving for LJP. Attention only needs to be paid to the ion sequence if automatic sorting is disabled.

When calculating LJP for a set of ions it is important to consider the sequence in which they are listed. Additional information can be found in [Marino et al., 2014](https://arxiv.org/abs/1403.3640) which describes the exact computational methods employed by LJPcalc.

* **The last ion's c0 may be overridden** to achieve electroneutrality on the c0 side. This will not occur if the sum of charge on the c0 side is zero.

* **cL for most ions will be slightly adjusted** to achieve electroneutrality on the cL side. The second-to-last ion's cL (which cannot equal its c0) will remain fixed, while the last cL will be adjusted to achieve electroneutrality. During the solving process all cL values (but the second-from-last) will be slightly adjusted. The adjustments are likely negligible experimentally, but this is why cL values in the output table slightly differ from those given for inputs.

### How to Correct for LJP in Electrophysiology Experiments

Electrophysiologists often measure (or clamp) the voltage of cells impaled with glass microelectrodes, but the difference in ionic composition between the intracellular (pipette) and extracellular (bath) solutions produces a LJP which is added to the measurements:

<p align="center">V<sub>measured</sub> = V<sub>cell</sub> + LJP</p>

**To compensate for LJP,** the electrophysiologist must calculate LJP mathematically (using software like LJPcalc) or estimate it experimentally ([instructions below](#measuring-ljp-experimentally)). Once the LJP is known, recorded data can be adjusted to accurately report cell voltage:

<p align="center">V<sub>cell</sub> = V<sub>measured</sub> - LJP</p>

> ⚠️ This method assumes that (1) the amplifier voltage was zeroed at the start of the experiment when the pipette was in open-tip configuration with the bath, and (2) the concentration of chloride (if using Ag/AgCl electrodes) in the internal and bath solutions are stable throughout the experiment.

#### Example LJP Calculation & Correction

This ion set came from in [Figl et al., 2003](https://medicalsciences.med.unsw.edu.au/sites/default/files/soms/page/ElectroPhysSW/AxoBits39New.pdf) Page 8. They have been loaded into LJPcalc such that the pipette solution is c0 and the bath solution is cL. Note that the order of ions has been adjusted to place the most abundant two ions at the bottom. This is ideal for LJPcalc's analytical method.

 Name       | Charge | pipette (mM) | bath (mM)      
------------|--------|--------------|---------
 K          | +1     | 145          | 2.8
 Na         | +1     | 13           | 145
 Mg         | +2     | 1            | 2
 Ca         | +2     | 0            | 1
 HEPES      | -1     | 5            | 5
 Gluconate  | -1     | 145          | 0           
 Cl         | -1     | 10           | 148.8

Loading this table into LJPcalc produces the following output:

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

> _[Figl et al., 2003](https://medicalsciences.med.unsw.edu.au/sites/default/files/soms/page/ElectroPhysSW/AxoBits39New.pdf) Page 8 calculated a LJP of 15.6 mV for this ion set (720 µV lesser magnitude than our calcualted LJP). As discussed above, differences in ion mobility table values and use of the Nernst-Planck vs. Henderson equation can cause commercial software to report values slightly different than LJPcalc. Experimentally these small differences are negligable, but values produced by LJPcalc are assumed to be more accurate. See [Marino et al., 2014](https://arxiv.org/abs/1403.3640) for discussion._

If we have patch-clamp data that indicates a neuron rests at -48.13 mV, what is its true resting potential? Now that we know the LJP, we can subtract it from our measurement:

<p align="center">V<sub>cell</sub> = V<sub>measured</sub> - LJP</p>

<p align="center">V<sub>cell</sub> = -48.13 - 16.05 mV</p>

<p align="center">V<sub>cell</sub> = -64.18 mV</p>

We now know our cell rests at -64.18 mV.

#### Notes about offset voltage, Ag/AgCl pellets, and and half-cell potentials

The patch-clamp amplifier is typically zeroed at the start of every experiment when the patch pipette is in open-tip configuration with the bath solution. An offset voltage (V<sub>offset</sub>) is applied such that the V<sub>measured</sub> is zero. This process nulls several potentials:

* _liquid_ junction potential (caused by internal vs. bath solutions)
* _half-cell_ potentials (caused by wire vs. internal and wire vs. bath)
  * these potentials are large and variable when [Cl] is low on either side
  * using an agar bridge helps keep this constant

When the amplifier is nulled prior to experiments the half-cell potentials can typically be ignored. However, if the [Cl] of the internal or bath solutions change during the course of an experiment (most likely to occur when an Ag/AgCl pellet is immersed in a flowing bath solution), the half-cell potentials become significant and affect V<sub>measured</sub> as they change. See [Figl et al., 2003](https://medicalsciences.med.unsw.edu.au/sites/default/files/soms/page/ElectroPhysSW/AxoBits39New.pdf) for more information about LJPs as they relate to electrophysiological experiments.

### Measuring LJP Experimentally

It is possible to measure LJP experimentally. However, this technique is discouraged because issues with KCl reference electrodes make it difficult to accurately measure LJP ([Barry and Diamond, 1970](https://link.springer.com/article/10.1007/BF01868010)). However, this technique can be used in cases when ion mobilities are not known:

To measure LJP of an intracellular vs. extracellular solution for whole-cell patch-clamp experiments:

* Fill the recording pipette with intracellular solution
* Fill the bath with the identical intracellular solution
* Use a high-mobility bath reference electrode with 3M KCl
  * you can use a pipette filled with 3M KCl 
  * you can use freshly cut 3M agar bridge
  * do not use an Ag/AgCl pellet (see note below)
* In current-clamp (I=0) adjust V<sub>offset</sub> so V<sub>measured</sub> is 0 mV
* Change the bath from pipette solution to extracellular solution
* If using an agar bridge, replace it with a new one
* Note  the measured voltage (it should be negative)
  * The inverse of this is the LJP (it should be positive)
  * Future recordings can be compensated: V<sub>cell</sub> = V<sub>measured</sub> - LJP

> ⚠️ Use of an Ag/AgCl pellet will not produce accurate results. This is because intracellular solution typically has a low [Cl]. Using a KCl reference is ideal because intracellular solution has high [K] and extracellular solution has high [Cl] so there is excellent mobility in all cases.

### Effect of Temperature on LJP

**The LJP is temperature dependent.** There are two sources of temperature-dependent variation: the Einstein relation and the conductivity table. The former can be easily defined at calculation time, while the latter requires modifying conductances in the ion mobility table. These modifications typically have a small effect on the LJP, so standard temperature (25C) can be assumed for most applications.

**The [Einstein relation](https://en.wikipedia.org/wiki/Einstein_relation_(kinetic_theory))** defines diffusion as **`D = µ * k * T`** where:

* **`D`** is the diffusion coefficient
* **`µ`** (mu) is [ionic mobility](https://en.wikipedia.org/wiki/Electrical_mobility)
* **`k`** is the [Boltzmann constant](https://en.wikipedia.org/wiki/Boltzmann_constant) (1.380649e-23 J / K)
* **`T`** is temperature (K)

**The ion conductivity table is temperature-specific.** Ion conductivity was measured experimentally and varies with temperature. The ion conductivity table here assumes standard temperature (25C), but ion conductivity values can be found for many ions at nonstandard temperatures. LJPcalc users desiring to perform LJP calculations at nonstandard temperatures are encouraged to build their own temperature-specific ion tables.

### Calculating Ionic Mobility from Charge and Conductivity

Ionic mobility is **`µ = Λ / (N * e² * |z|)`** where:

* **`µ`** (mu) is [ionic mobility](https://en.wikipedia.org/wiki/Electrical_mobility) (m² / V / sec)
* **`Λ`** (Lambda) is [molar conductivity](https://en.wikipedia.org/wiki/Molar_conductivity) (S * cm²/ mol)
* **`N`** is the [Avogadro constant](https://en.wikipedia.org/wiki/Avogadro_constant) (6.02214076e23 particles / mol)
* **`e`** is the [elementary charge](https://en.wikipedia.org/wiki/Elementary_charge) (1.602176634e-19 Coulombs)
* **`z`** is the absolute value of the [elementary charge](https://en.wikipedia.org/wiki/Elementary_charge) of the ion

### References
* **[Marino et al. (2014)](https://arxiv.org/abs/1403.3640)** - describes a computational method to calculate LJP according to the stationary Nernst-Planck equation. The JAVA software described in this manuscript is open-source and now on GitHub ([JLJP](https://github.com/swharden/jljp)). Figure 1 directly compares LJP calculated by the Nernst-Planck vs. Henderson equation.
* **[Perram and Stiles (2006)](https://pubs.rsc.org/en/content/articlelanding/2006/cp/b601668e)** - A review of several methods used to calculate liquid junction potential. This manuscript provides excellent context for the history of LJP calculations and describes the advantages and limitations of each.
* **[Shinagawa (1980)](https://www.ncbi.nlm.nih.gov/pubmed/7401663)** _"Invalidity of the Henderson diffusion equation shown by the exact solution of the Nernst-Planck equations"_ - a manuscript which argues that the Henderson equation is inferior to solved Nernst-Planck-Poisson equations due to how it accounts for ion flux in the charged diffusion zone.
* **[Lin (2011)](http://www.sci.osaka-cu.ac.jp/~ohnita/2010/TCLin.pdf)** _"The Poisson The Poisson-Nernst-Planck (PNP) system for ion transport (PNP) system for ion transport"_ - a PowerPoint presentation which reviews mathematical methods to calculate LJP with notes related to its application in measuring voltage across cell membranes.
* **[Nernst-Planck equation](https://en.wikipedia.org/wiki/Nernst%E2%80%93Planck_equation)** (Wikipedia)
* **[Goldman Equation](https://en.wikipedia.org/wiki/Goldman_equation)** (Wikipedia)
* **[EGTA charge and pH](https://www.sciencedirect.com/science/article/pii/S0165027099000369?via%3Dihub#FIG1)** - Empirical determination of EGTA charge state distribution as a function of pH.

## Citing LJPcalc

If LJPcalc facilitated your research, consider citing this project by name so it can benefit others too:

> "Liquid junction potential was calculated according to the stationary Nernst-Planck equation using LJPcalc¹"
>
> [1] Harden, SW and Brogioli, D (2020). LJPcalc v1.0. [Online]. Available: https://github.com/swharden/LJPcalc, Accessed on: Feb. 16, 2020.

## Authors
LJPcalc was created by [Scott W Harden](http://swharden.com/) in 2020. LJPcalc began as C# port of [JLJP](https://github.com/swharden/JLJP) by [Doriano Brogioli](https://sites.google.com/site/dbrogioli/) originally published on SourceForge in 2013. LJPcalc is heavily influenced by [Marino et al., 2014](https://arxiv.org/abs/1403.3640).