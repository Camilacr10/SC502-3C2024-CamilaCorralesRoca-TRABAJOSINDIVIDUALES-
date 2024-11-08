<?php

$transacciones = [];

function registrarTransaccion($id, $descripcion, $monto) {
    global $transacciones;
    $transacciones[] = [
        'id' => $id,
        'descripcion' => $descripcion,
        'monto' => $monto
    ];
}

function generarEstadoDeCuenta() {
    global $transacciones;

    $montoContado = 0;

    foreach ($transacciones as $transaccion) {
        $montoContado += $transaccion['monto'];
    }

    $montoConInteres = $montoContado * 1.026;

    $cashBack = $montoContado * 0.001;

    $montoFinal = $montoConInteres - $cashBack;

    echo "Estado de Cuenta:\n";
    echo "----------------------------------\n";
    echo "Transacciones:\n";
    foreach ($transacciones as $transaccion) {
        echo "ID: {$transaccion['id']} - Descripción: {$transaccion['descripcion']} - Monto: $ {$transaccion['monto']}\n";
    }
    echo "----------------------------------\n";
    echo "Monto Total de Contado: $ " . number_format($montoContado, 2) . "\n";
    echo "Monto Total con Interés (2.6%): $ " . number_format($montoConInteres, 2) . "\n";
    echo "Cash Back (0.1%): $ " . number_format($cashBack, 2) . "\n";
    echo "Monto Final a Pagar: $ " . number_format($montoFinal, 2) . "\n";

    $archivo = fopen("estado_cuenta.txt", "w");
    fwrite($archivo, "Estado de Cuenta:\n");
    fwrite($archivo, "----------------------------------\n");
    fwrite($archivo, "Transacciones:\n");
    foreach ($transacciones as $transaccion) {
        fwrite($archivo, "ID: {$transaccion['id']} - Descripción: {$transaccion['descripcion']} - Monto: $ {$transaccion['monto']}\n");
    }
    fwrite($archivo, "----------------------------------\n");
    fwrite($archivo, "Monto Total de Contado: $ " . number_format($montoContado, 2) . "\n");
    fwrite($archivo, "Monto Total con Interés (2.6%): $ " . number_format($montoConInteres, 2) . "\n");
    fwrite($archivo, "Cash Back (0.1%): $ " . number_format($cashBack, 2) . "\n");
    fwrite($archivo, "Monto Final a Pagar: $ " . number_format($montoFinal, 2) . "\n");
    fclose($archivo);

    echo "El estado de cuenta también se ha guardado en el archivo estado_cuenta.txt\n";
}

registrarTransaccion(1, "Compra en supermercado", 150.00);
registrarTransaccion(2, "Pago de servicios", 200.00);
registrarTransaccion(3, "Compra en tienda de ropa", 75.50);
registrarTransaccion(4, "Suscripción mensual", 50.00);

generarEstadoDeCuenta();


