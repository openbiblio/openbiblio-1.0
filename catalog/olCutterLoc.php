<?PHP
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	function getCutter ($s) {
		##### variation of US Library of Congress rules for US English
		//echo "cutter input string: " . $s . "<br />";
		$s = strtolower($s);
		$s2 = substr($s,0,2);
		$ch1 = substr($s,0,1);
		$ch2 = substr($s,1,1);
		switch ($ch1) {
			case 'a':; case 'e':; case 'i':; case 'o':; case 'u':
				switch ($ch2) {
					case 'b':; $n2 = '2'; break;
					case 'd':; $n2 = '3'; break;
					case 'l':; case 'm': $n2 = '4'; break;
					case 'n':; $n2 = '5'; break;
					case 'p':; $n2 = '6'; break;
					case 'r':; $n2 = '7'; break;
					case 's':; case 't': $n2 = '8'; break;
					case 'u':; case 'v':; case 'w':; case 'x':; case 'y': $n2 = '9'; break;
				}
				break;
			case 's':	 
				switch ($ch2) {
					case 'a':; $n2 = '2'; break;
					case 'e':; $n2 = '4'; break;
					case 'h':; case 'i':; $n2 = '5'; break;
					case 'm':; case 'n':; case 'o':; case 'p':; $n2 = '6'; break;
					case 't':; $n2 = '7'; break;
					case 'u':; $n2 = '8'; break;
					case 'w':; case 'x':; case 'y':; case 'z': $n2 = '9'; break;
					default: if (substr($s,1,2) == 'ch') $n2 = '3'; break;
				}
				break;
			default:
				switch ($ch2) {
					case 'a':; case 'b':; case 'c':; case 'd':; $n2 = '3'; break;
					case 'e':; case 'f':; case 'g':; case 'h':; $n2 = '4'; break;
					case 'i':; case 'j':; case 'k':; case 'l':; case 'm':; case 'n':; $n2 = '5'; break;
					case 'o':; case 'p':; case 'q':; $n2 = '6'; break;
					case 'r':; case 's':; case 't':; $n2 = '7'; break;
					case 'u':; case 'v':; case 'w':; case 'x':; $n2 = '8'; break;
					case 'y':; case 'z':; $n2 = '9'; break;
				}
		}
		$cutter = ucfirst($ch1) . $n2;
		for ($i= 2; $i<=3; $i++) {
			$ch = substr($s, $i, 1);
			switch ($ch) {
					case 'a':; case 'b':; case 'c':; case 'd':; $n = '3'; ;break;
					case 'e':; case 'f':; case 'g':; case 'h':; $n = '4'; ;break;
					case 'i':; case 'j':; case 'k':; case 'l':; $n = '5'; break;
					case 'm':; case 'n':; case 'o':; $n = '6'; break;
					case 'p':; case 'q':; case 'r':; case 's': $n = '7'; ;break;
					case 't':; case 'u':; case 'v':; $n = '8'; break;
					case 'w':; case 'x':; case 'y':; case 'z':; $n = '9'; ;break;
			}
			$cutter .= $n;
		}
		return $cutter;
	}
	//echo "loaded lc_cutter file. <br />";
?>
