<?php
if($pets) {	
	if($_GET['petname']) {
		LOGDEB("Parameter: petname=\"".$_GET['petname']."\"");
	}	
	
	$found = false;
	foreach($pets AS $pet) {
		$multi = $pet['name']."@";
		
		if($pet['name'] == $_GET['petname'] OR empty($_GET['petname'])){
			$found = true;
			// ID
			$petid = $pet['id'];
			print $multi."PetID@".$petid."<br>";
			LOGINF("PetID@".$petid);
			// Name
			$petname = $pet['name'];
			print $multi."PetName@".$petname."<br>";
			LOGINF("PetName@".$petname);
			// Description
			print $multi."PetDescription@".$pet['comments']."<br>";
			// Date of birth
			$pet_dob = strtotime($pet['date_of_birth']);
			print $multi."PetDob@".date('d.m.Y', $pet_dob)."<br>";
			print $multi."PetDobLox@".epoch2lox($pet_dob)."<br>";
			// Weight
			print $multi."PetWeight@".$pet['weight']." kg<br>";
			// Gender
			print $multi."PetGenderID@".$pet['gender']."<br>";
			if($pet['gender'] == 0) {
				print $multi."PetGender@Female<br>";
			} else {
				print $multi."PetGender@Male<br>";
			}
			// Species
			print $multi."PetSpeciesID@".$pet['species_id']."<br>";
			if($pet['species_id'] == 2) {
				print $multi."PetSpecies@Dog<br>";
			} else {
				print $multi."PetSpecies@Cat<br>";
			}
			// Location
			$curr_location_id = $pet['position']['where'] - 1;
			if($curr_location_id == 0) {
				$curr_location = "Inside";
			} else {
				$curr_location = "Outside";
			}
			print $multi."PetLocationID@".$curr_location_id."<br>";
			$curr_location_id = $pet['position']['where'];
			print $multi."PetLocation@$curr_location<br>";
			LOGINF("PetLocation@".$curr_location);		
			// Last location time			
			$pet_loc_time = strtotime($pet['position']['since']);
			print $multi."PetLocationSince@".date('d.m.Y H:i:s', $pet_loc_time)."<br>";
			print $multi."PetLocationSinceLox@".epoch2lox($pet_loc_time)."<br>";
			print "<br>";
		}	
	}
	if(!$found) {		
		print "Pet does not match!<br>";
		LOGWARN("Pet does not match!");
	}
} else {	
	print "No pets!<br";
	LOGERR("No pets!");
}
?>
