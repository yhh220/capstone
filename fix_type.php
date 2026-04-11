<?php
$base = __DIR__ . '/app/Filament/Resources';
$count = 0;
foreach(glob("$base/*/*Resource.php") as $f) {
    if (is_file($f)) {
        $c = file_get_contents($f);
        $newC = str_replace('protected static ?string $navigationGroup', 'protected static \UnitEnum|string|null $navigationGroup', $c);
        if ($c !== $newC) {
            file_put_contents($f, $newC);
            $count++;
        }
    }
}
echo "Fixed $count files.\n";
