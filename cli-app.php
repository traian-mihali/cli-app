<?php

require 'lib'. DIRECTORY_SEPARATOR .'rb.php';

R::setup('sqlite:database\bottles.db');

$opts = getopt("", ["add:", "list", "delete:", "note:", "attach-to:", "notes:", "remove-note:"]);

$cmd_options = "
1. --add=\"bottle_name\" 
2. --list  
3. --delete=bottle_id 
4. --note=\"bottle_note\" --attach-to=bottle_id, 
5. --notes=bottle_id 
6. --remove-note=bottle_note_id";

if (!count($opts)) {
    die("Invalid command. Please use one of the options below:\n{$cmd_options}\n");
}

if (isset($opts["add"])) {
    $bottle = R::dispense("bottle");
    $bottle->name = strtolower($opts["add"]);
    R::store($bottle);

    die("OK\n");
}

else if (isset($opts["delete"])) {
    $bottle = R::load("bottle", $opts["delete"]);
    if (!$bottle->id)
        die("Bottle not found.\n");

    R::trash($bottle);

    die("The following bottle has been removed: {$bottle->name}\n");
}

else if (isset($opts["list"])) {
    $bottles = R::find("bottle");
    if (!count($bottles))
        die("There are no bottles available.\n");
        
    foreach($bottles as $bottle)
        echo "#{$bottle->id}: {$bottle->name} \n";

    exit;
}

else if (isset($opts["note"]) && isset($opts["attach-to"])) {
    $bottle = R::load("bottle", $opts["attach-to"]);
    if(!$bottle->id)
        die("Bottle not found.\n");

    $note = R::dispense("note");
    $note->description = $opts["note"];
    $bottle->xownNoteList[] = $note;
    R::store($bottle);

    die("Note added to bottle: {$bottle->name}\n");
}

else if (isset($opts["notes"])) {
    $bottle = R::load("bottle", $opts["notes"]);
    if (!$bottle->id)
        die("Bottle not found.\n");

    foreach($bottle->xownNoteList as $note)
        echo " #{$note->id}: {$note->description} \n";

    exit;
}

else if (isset($opts["remove-note"])) {
    $note = R::load("note", $opts["remove-note"]);
    if (!$note->id)
        die("Note not found.\n");

    R::trash($note);
    
    die("The following note has been removed: {$note->description}\n");
}

else {
    die("Invalid command. Please use one of the options below:\n{$cmd_options}\n");
}

