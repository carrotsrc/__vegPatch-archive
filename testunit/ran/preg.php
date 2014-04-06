<?php


if(preg_match("/[\Q^><&\E]/", "id(User())(Area()Area('home'));"))
	echo "Yes";
else
	echo "No";
?>
