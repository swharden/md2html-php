<?php

// this script serves the latest 20 posts as an RSS feed

require('../blogFunctions.php');
echoBlogFeed(20);