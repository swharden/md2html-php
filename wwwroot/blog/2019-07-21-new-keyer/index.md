---
title: New QRSS Keyer
date: 2019-07-21 21:00:00
tags: qrss, circuit
---

# New QRSS Keyer

**I'd like to use an interesting pattern** that takes advantage of both FSK and OOK. I want it to be 5 Hz or less in bandwidth, and I want it to be unique and recognizable in cases where only a few seconds are spotted, but I don't want it to be so odd that it's annoying. I came up with something like:

<div class="text-center">

![](pattern.jpg)

</div>

Here's what it looks like on the air:

<div class="text-center img-border">

![](WD4ELG-30.1907210830.d1c3ee1f06.jpg)

</div>

### Microcontroller
ATTiny2313 programmed with C ([source code](https://github.com/swharden/QRSS-hardware/tree/master/builds/keyer/main.c))

### GPS serial data parsing
To ensure the message transmits exactly at the 0:00 mark of every 10 minutes, the microcontroller is occasionally put into a "wait" mode where it continuously watches the GPS output (parsing the serial data that bursts out every second) and waits for the minutes digit to become zero before beginning a transmission.

Technical details: The output is 9600 baud serial data in [NMEA format](https://www.gpsinformation.org/dale/nmea.htm). A string buffer is filled as incoming characters are received. If the message starts with `$GPRMC` we know the 11th character is the ones digit of the minutes number in the time code. Waiting for the next ten minute rollover to occur is as easy as waiting until that character becomes zero.

```
$GPRMC,184130.00,...
^    ^     ^
```

The start of a time message looks like this. To identify `$GPRMC` we just need to match the `$` and the `C` (indicated by the first two arrows above). We then know if we keep reading, we will arrive at the ones digit of the minutes number (the third arrow).

I have some notes on the [Neo-6M here](https://github.com/swharden/AVR-projects/tree/master/uBlox%20Neo-6M)

### FSK Circuit

I found this design very convenient. A potentiometer (RV1) sets center frequency to let me adjust where in the QRSS band I want to transmit. 

The FSK input (which could be digital or analog) is 0-5V, expected to originate from a microcontroller pin. The keyer is programmed to transmits over the full 0-5V range.

The second potentiometer (RV2) sets the width of the FSK input. I adjust this to achieve a bandwidth of about 5 Hz.

The output is buffered, mixed, and sent to the oscillator module with coax.

<div class="text-center">

![](fsk-circuit.jpg)

</div>

<div class="text-center img-border">

![](2019-07-19-keyer.jpg)
![](2019-07-19-modules.jpg)
![](2019-07-31.jpg)

</div>
