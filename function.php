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
        header('location:index.php');
    }
}

//menambah barang masuk

if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    $addtomasuk = mysqli_query($conn, "insert into masuk (idbarang, keterangan, qty) values ('$barangnya', '$keterangan', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock = '$tambahkanstocksekarangdenganquantity' where idbarang = '$barangnya'");

    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    } else {
        echo 'Gagal';
        header('location:masuk.php');
    }
}


if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $kurangkanstocksekarangdenganquantity = $stocksekarang - $qty;

    $addtokeluar = mysqli_query($conn, "insert into keluar (idbarang, penerima, qty) values ('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock = '$kurangkanstocksekarangdenganquantity' where idbarang = '$barangnya'");

    if($addtokeluar&&$updatestockmasuk){
        header('location:keluar.php');
    } else {
        echo 'Gagal';
        header('location:keluar.php');
    }
}



// update info barang
if(isset($_POST['updatebarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "update stock set namabarang = '$namabarang', deskripsi = '$deskripsi' where idbarang = '$idb'");
    if($update){
        header('location: index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

// menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $delete = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
    if($delete){
        header('location: index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

// mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    // Ambil stock saat ini
    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    // Ambil qty sebelumnya
    $qtyskrng = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk = '$idm'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtylama = $qtynya['qty'];

    // Step 1: Balikin dulu stock-nya
    $stockkembali = $stockskrng - $qtylama;

    // Step 2: Tambahin qty baru ke stock
    $stockbaru = $stockkembali + $qty;

    // Update stock dan data masuk
    $updatestock = mysqli_query($conn, "UPDATE stock SET stock='$stockbaru' WHERE idbarang = '$idb'");
    $updatemasuk = mysqli_query($conn, "UPDATE masuk SET qty='$qty', keterangan='$keterangan' WHERE idmasuk='$idm'");

    if($updatestock && $updatemasuk){
        header('location: masuk.php');
    } else {
        echo 'Gagal';
        header('location: masuk.php');
    }
}

// Edit barang keluar
if (isset($_POST['updatebarangkeluar'])) {
    $idk = $_POST['idk']; // ID barang keluar
    $idb = $_POST['idb']; // ID barang
    $qty = $_POST['qty']; // Jumlah barang keluar baru
    $penerima = $_POST['penerima']; // Penerima barang
    $qtylama = $_POST['qtylama']; // Jumlah barang keluar lama

    // Cek stok sekarang
    $cekstok = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($cekstok);
    $stoksekarang = $data['stok']; // Stok yang ada saat ini

    // Hitung selisih perubahan jumlah barang keluar
    $selisih = $qty - $qtylama; // Selisih antara qty baru dan qty lama

    if ($selisih >= 0) {
        // Barang keluar bertambah, stok harus dikurangi
        if ($stoksekarang >= $selisih) {
            $stokbaru = $stoksekarang - $selisih; // Kurangi stok
        } else {
            // Jika stok tidak cukup untuk mengurangi barang keluar
            echo "<script>alert('Stok tidak cukup untuk perubahan ini!');window.location.href='keluar.php';</script>";
            exit;
        }
    } else {
        // Barang keluar dikurangi, stok harus ditambah
        $stokbaru = $stoksekarang + abs($selisih); // Tambahkan stok
    }

    // Update stok dan data barang keluar
    $updatestok = mysqli_query($conn, "UPDATE stock SET stok='$stokbaru' WHERE idbarang='$idb'");
    $updatekeluar = mysqli_query($conn, "UPDATE keluar SET qty='$qty', penerima='$penerima' WHERE idkeluar='$idk'");

    if ($updatestok && $updatekeluar) {
        // Redirect jika update berhasil
        header('location:keluar.php');
    } else {
        echo "Gagal update data";
    }
}




?>