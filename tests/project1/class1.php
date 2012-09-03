<?php

class Class1 {

	public function foo() {
		require('file4.php');

		if (1) {
			if (1) {
				if (1) {
					require('file5.php');
				} else {
					require('file6.php');
				}
			} else {
				require('file6.php');
			}
		} else {
			require('file6.php');
		}
	}

};
