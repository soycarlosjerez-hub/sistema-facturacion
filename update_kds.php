<?php

use Illuminate\Support\Facades\DB;

DB::table('productos')->whereNull('incluir_kds')->update(['incluir_kds' => true]);
DB::table('productos')->where('incluir_kds', 0)->update(['incluir_kds' => 1]);

echo "Productos actualizados a incluir_kds = true\n";
