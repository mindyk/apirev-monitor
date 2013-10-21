<?php

namespace Apirevmonitor\Map;


class Game implements Map {

	public function resourceToGame($resourceId) {
		if ($resourceId < 100) {
			return 'staemme';
		} else if ($resourceId < 150) {
			return 'foe';
		}  else {
			return 'grepo';
		}
	}
}

Interface Map {

}