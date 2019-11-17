<?php
$array = array('Cero','Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve','Diez');
$array = array_move_elem($array, 3, 5); // Move element in position 3 to position 5...
print_r($array);

$array = array_move_elem($array, 5, 3); // Move element in position 5 to position 3, leaving array as it was... ;)
print_r($array);

?>