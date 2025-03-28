<?php
session_start();
//membuat koneksi ke db

$conn = mysqli_connect("localhost", "root","", "stockbarang");


//menambah barang baru

if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn, "insert into stock (namabarang, deskripsi, stock) values('$namabarang', '$deskripsi', '$stock')");
    if($addtotable){
        header('location: index.php');
    } else {
        echo 'Gagal';
        header('location:inddex.php');
    }
}

//menambah barang masuk

if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];

    $addtomasuk = mysqli_query($conn, "insert into masuk (idbarang, keterangan) values ('$barangnya', '$penerima')");
    if($addtomasuk){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }

}
?>