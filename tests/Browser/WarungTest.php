<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Warung;


class WarungTest extends DuskTestCase
{
    
    /**
     * A Dusk test example.
     *
     * @return void
     */
     public function testTambahWarung(){

        $this->browse(function ($WarungTest) {
            $WarungTest->loginAs(User::find(1))
                  ->visit('/warung')
                  ->clickLink('Tambah Warung')
                  ->type('name','Fahri')
                  ->type('no_telpon','085675753645')
                  ->type('email','Testswarung@gmail.com')
                  ->type('nama_bank','BNI')
                  ->type('atas_nama','Jajang')
                  ->type('no_rek','634735432')
                  ->type('alamat','Jl. Testing');
                  $WarungTest->script("document.getElementById('pilih_kelurahan').selectize.setValue('1');");  
                  $WarungTest->assertSee('Kedaton');
                  $WarungTest->press('#btnSimpanWarung')
                  ->assertSee('BERHASIL : MENAMBAH WARUNG FAHRI');
        });

    } 
  public function testUniqueWarung(){

        $this->browse(function ($WarungTest) {
            $WarungTest->loginAs(User::find(1))
                  ->visit('/warung')
                  ->clickLink('Tambah Warung')
                  ->type('name','Fahri')
                  ->type('no_telpon','085675753645')
                  ->type('email','Testswarung@gmail.com')
                  ->type('nama_bank','BNI')
                  ->type('atas_nama','Jajang')
                  ->type('no_rek','634735432')
                  ->type('alamat','Jl. Testing');
                  $WarungTest->script("document.getElementById('pilih_kelurahan').selectize.setValue('1');");  
                  $WarungTest->assertSee('Kedaton');
                  $WarungTest->press('#btnSimpanWarung');
                  $WarungTest->script("document.getElementById('nama_warung').focus();");
                  $WarungTest->assertSeeIn("#nama_warung_error","Maaf name Sudah Terpakai");
                  $WarungTest->script("document.getElementById('no_telpon').focus();");
                  $WarungTest->assertSeeIn("#no_telp_error","Maaf no telpon Sudah Terpakai");
                  $WarungTest->script("document.getElementById('no_rek').focus();");
                  $WarungTest->assertSeeIn("#no_rek_error","Maaf no rek Sudah Terpakai");
        });

    } 

      public function testUbahWarung() {
      $warung = Warung::select('id')->where('no_telpon','085675753645')->first();
      $this->browse(function ($WarungTest)use($warung) {
        $WarungTest->loginAs(User::find(1))
                  ->visit('/warung')
                  ->assertSeeLink('Tambah Warung')
                  ->whenAvailable('.js-confirm', function ($table) { 
                              ;
                    })
                  ->with('.table', function ($table) use($warung){
                        $table->assertSee('Fahri')
                              ->press('#edit-'.$warung->id);
                    })
                  ->assertSee('Edit Warung')
                  ->type('name','Fahrizal Ramadhan')
                  ->type('no_telpon','08567767975')
                  ->type('nama_bank','BNI SYARIAH')
                  ->type('atas_nama','Jajang NUrjaman')
                  ->type('no_rek','634735436432')
                  ->type('alamat','Jl. Testing Browser');
                  $WarungTest->script("document.getElementById('pilih_kelurahan').selectize.setValue('2');");  
                  $WarungTest->assertSee('Surabaya');
                  $WarungTest->press('#btnSimpanWarung')
                  ->assertSee('BERHASIL : MENGUBAH WARUNG FAHRIZAL RAMADHAN');
        });

    } 

       public function testHapusWarung() {
      $warung = Warung::select('id')->where('no_telpon','08567767975')->first();
      $this->browse(function ($WarungTest)use($warung) {
        $WarungTest->loginAs(User::find(1))
                  ->visit('/warung')
                  ->assertSeeLink('Tambah Warung')
                  ->whenAvailable('.js-confirm', function ($table) { 
                              ;
                    })
                  ->with('.table', function ($table) use($warung){
                        $table->assertSee('Fahrizal Ramadhan')
                              ->press('#delete-'.$warung->id)
                              ->assertDialogOpened('Yakin Mau Menghapus Warung Fahrizal Ramadhan?');
                    })->driver->switchTo()->alert()->accept();
                  $WarungTest->assertSee('BERHASIL : MENGHAPUS WARUNG');
        });

    } 
}
