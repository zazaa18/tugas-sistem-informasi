<?php
require_once "User.php";
require_once "Cetak.php";

class Mahasiswa extends User implements CetakLaporan {
    private $jurusan;
    private $semester;
    private $dosen;
    private $nilai = [];

    public function __construct($nama, $nim, $jurusan, $semester, $dosen){
        parent::__construct($nama, $nim);
        $this->jurusan = $jurusan;
        $this->semester = $semester;
        $this->dosen = $dosen;
    }

    public function getNama(){
        return $this->nama;
    }

    public function getId(){
        return $this->nim;
    }

    public function getInfo(){
        return "
            <p><strong>Nama</strong> : {$this->nama}</p>
            <p><strong>NIM</strong> : {$this->nim}</p>
            <p><strong>Jurusan</strong> : {$this->jurusan}</p>
            <p><strong>Semester</strong> : {$this->semester}</p>
        ";
    }

    public function addNilai($matkul, $nilai, $sks){

        $kode = "-";

        if(isset($_SESSION['matakuliah'])){
            foreach($_SESSION['matakuliah'] as $k => $mk){
                if($mk['nama'] == $matkul){
                    $kode = $k;
                    break;
                }
            }
        }

        $this->nilai[] = [
            'kode' => $kode,
            'matkul' => $matkul,
            'nilai' => $nilai,
            'sks' => $sks
        ];
    }

    public function resetNilai(){
        $this->nilai = [];
    }

    private function getGrade($nilai){
        if($nilai >= 85) return "A";
        elseif($nilai >= 75) return "B";
        elseif($nilai >= 65) return "C";
        elseif($nilai >= 50) return "D";
        else return "E";
    }

    private function getMutu($nilai){
        if($nilai >= 85) return 4;
        elseif($nilai >= 75) return 3;
        elseif($nilai >= 65) return 2;
        elseif($nilai >= 50) return 1;
        else return 0;
    }

    public function cetakLaporan(){
        echo "<h3>Daftar Nilai</h3>";

        echo "<table border='1' cellpadding='8' cellspacing='0' width='100%'>";
        echo "<tr>
                <th>No</th>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th>Nilai</th>
                <th>Grade</th>
                <th>Mutu</th>
                <th>SKS</th>
              </tr>";

        $no = 1;
        $totalMutu = 0;
        $totalSks = 0;

        if(empty($this->nilai)){
            echo "<tr><td colspan='7'>Belum ada nilai</td></tr>";
        } else {
            foreach($this->nilai as $n){

                $nilai = $n['nilai'] ?? 0;
                $sks   = $n['sks'] ?? 0;

                $grade = $this->getGrade($nilai);
                $mutu  = $this->getMutu($nilai);

                $totalMutu += $mutu * $sks;
                $totalSks  += $sks;

                echo "<tr>
                        <td>$no</td>
                        <td>" . ($n['kode'] ?? '-') . "</td>
                        <td>" . ($n['matkul'] ?? '-') . "</td>
                        <td>" . ($nilai ?? '-') . "</td>
                        <td>$grade</td>
                        <td>$mutu</td>
                        <td>" . ($sks ?? '-') . "</td>
                      </tr>";

                $no++;
            }
        }

        echo "</table>";

        $ipk = ($totalSks > 0) ? $totalMutu / $totalSks : 0;

        echo "<br><strong>IPK : " . number_format($ipk,2) . "</strong>";

        // ✅ FIX ERROR DI SINI (NULL DOSEN)
        $namaDosen = ($this->dosen != null) ? $this->dosen->getNama() : "-";
        $nimDosen  = ($this->dosen != null) ? $this->dosen->getNim() : "-";

        echo "<br><br><div style='text-align:right'>
                <strong>Dosen Wali</strong><br>
                {$namaDosen}<br>
                {$nimDosen}
              </div>";
    }
}
?>