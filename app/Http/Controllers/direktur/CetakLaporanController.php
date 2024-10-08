<?php

namespace App\Http\Controllers\direktur;

use App\Charts\PenjualanRumahChart;
use App\Http\Controllers\Controller;
use App\Models\BlokPerumahan;
use App\Models\KeluhanPelanggan;
use App\Models\Pembayaran;
use App\Models\PembayaranUangMuka;
use App\Models\PendaftaranPemesanan;
use App\Models\Promo;
use App\Models\RiwayatPembangunanRumah;
use App\Models\Rumah;
use App\Models\RumahPelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CetakLaporanController extends Controller
{
    public function rumah()
    {
        $query = Rumah::select("*");
        $filter = [
            'blok_perumahan' => 'Semua Blok Perumahan',
            'status_ketersediaan' => 'Semua Status Ketersediaan',
            'status_pembangunan' => 'Semua Status Pembangunan'
        ];

        if (request()->get('id_blok_perumahan')) {
            $query = $query->where('id_blok_perumahan', request()->get('id_blok_perumahan'));
            $filter['blok_perumahan'] = BlokPerumahan::find(request()->get('id_blok_perumahan'))->first()->nama;
        }

        if (request()->get('status_ketersediaan')) {
            $query = $query->where('status_ketersediaan', request()->get('status_ketersediaan'));
            if (request('status_ketersediaan') == 1)
                $filter['status_ketersediaan'] = "Tersedia";
            elseif (request('status_ketersediaan') == 2)
                $filter['status_ketersediaan'] = "Dipesan";
            elseif (request('status_ketersediaan') == 3)
                $filter['status_ketersediaan'] = "Terjual";
        }

        if (request()->get('status_pembangunan')) {
            $query = $query->where('status_pembangunan', request()->get('status_pembangunan'));
            if (request('status_pembangunan') == 1)
                $filter['status_pembangunan'] = "Belum Dibangun";
            elseif (request('status_pembangunan') == 2)
                $filter['status_pembangunan'] = "Dalam Tahap Pembangunan";
            elseif (request('status_pembangunan') == 3)
                $filter['status_pembangunan'] = "Selesai Dibangun";
        }

        $rumah = $query->get()->map(function ($item) {
            if ($item->status_ketersediaan == 1)
                $item->status_ketersediaan = "Tersedia";
            elseif ($item->status_ketersediaan == 2)
                $item->status_ketersediaan = "Dipesan";
            elseif ($item->status_ketersediaan == 3)
                $item->status_ketersediaan = "Terjual";

            if ($item->status_pembangunan == 1)
                $item->status_pembangunan = "Belum Dibangun";
            elseif ($item->status_pembangunan == 2)
                $item->status_pembangunan = "Dalam Tahap Pembangunan";
            elseif ($item->status_pembangunan == 3)
                $item->status_pembangunan = "Selesai Dibangun";

            return $item;
        });

        return view('dashboard.admin.halaman.laporan.cetak.laporan.rumah', [
            'rumah' => $rumah,
            'filter' => $filter
        ]);
    }

    public function penjualanRumah()
    {
        $query = PembayaranUangMuka::orderBy('created_at', 'DESC');
        $filter = [
            'blok_perumahan' => 'Semua Blok Perumahan',
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $query = $query->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }

        if (request()->get('id_blok_perumahan')) {
            $query = $query->whereHas('rumahPelanggan', function ($q) {
                $q->whereHas('rumah', function ($q) {
                    $q->where('id_blok_perumahan', request()->get('id_blok_perumahan'));
                });
            });
            $filter['blok_perumahan'] = BlokPerumahan::find(request()->get('id_blok_perumahan'))->first()->nama;
        }

        $penjualan_rumah = $query->get()->map(function ($item) {
            $tanggal = new Carbon($item->tanggal);
            $item->tanggal = $tanggal->day . ' ' . $tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal->year;
            return $item;
        });

        return view('dashboard.admin.halaman.laporan.cetak.laporan.penjualan_rumah', [
            'penjualan_rumah' => $penjualan_rumah,
            'filter' => $filter
        ]);
    }

    public function pemesananRumah()
    {
        $query = PendaftaranPemesanan::orderBy('created_at', 'DESC');
        $filter = [
            'blok_perumahan' => 'Semua Blok Perumahan',
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $query = $query->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }

        if (request()->get('id_blok_perumahan')) {
            $query = $query->whereHas('rumahPelanggan', function ($q) {
                $q->whereHas('rumah', function ($q) {
                    $q->where('id_blok_perumahan', request()->get('id_blok_perumahan'));
                });
            });
            $filter['blok_perumahan'] = BlokPerumahan::find(request()->get('id_blok_perumahan'))->first()->nama;
        }

        $pemesanan_rumah = $query->get()->map(function ($item) {
            $tanggal = new Carbon($item->tanggal);
            $item->tanggal = $tanggal->day . ' ' . $tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal->year;
            return $item;
        });

        return view('dashboard.admin.halaman.laporan.cetak.laporan.pemesanan_rumah', [
            'pemesanan_rumah' => $pemesanan_rumah,
            'filter' => $filter
        ]);
    }

    public function keluhanPelanggan()
    {
        $query = KeluhanPelanggan::orderBy('tanggal', 'DESC');
        $filter = [
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $query = $query->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }

        $keluhan_pelanggan = $query->get()->map(function ($item) {
            $tanggal = new Carbon($item->tanggal);
            $item->tanggal = $tanggal->day . ' ' . $tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal->year;
            return $item;
        });


        return view('dashboard.admin.halaman.laporan.cetak.laporan.keluhan_pelanggan', [
            'keluhan_pelanggan' => $keluhan_pelanggan,
            'filter' => $filter
        ]);
    }

    public function progresPembangunanRumah()
    {
        $query = RiwayatPembangunanRumah::orderBy('created_at', 'DESC');
        $filter = [
            'blok_perumahan' => 'Semua Blok Perumahan',
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $query = $query->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }

        if (request()->get('id_blok_perumahan')) {
            $query = $query->whereHas('rumah', function ($q) {
                $q->where('id_blok_perumahan', request()->get('id_blok_perumahan'));
            });

            $filter['blok_perumahan'] = BlokPerumahan::find(request()->get('id_blok_perumahan'))->first()->nama;
        }

        $progres_pembangunan_rumah = $query->get()->map(function ($item) {
            $tanggal = new Carbon($item->tanggal);
            $item->tanggal = $tanggal->day . ' ' . $tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal->year;
            return $item;
        });


        return view('dashboard.admin.halaman.laporan.cetak.laporan.progres_pembangunan_rumah', [
            'progres_pembangunan_rumah' => $progres_pembangunan_rumah,
            'filter' => $filter
        ]);
    }

    public function promo()
    {
        $query = Promo::orderBy('tanggal_mulai', 'DESC');
        $filter = [
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $query = $query->where(function ($q) {
                $q->where('tanggal_mulai', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal_mulai', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }

        $promo = $query->get()->map(function ($item) {
            $tanggal_mulai = new Carbon($item->tanggal_mulai);
            $item->tanggal_mulai = $tanggal_mulai->day . ' ' . $tanggal_mulai->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal_mulai->year;

            $tanggal_selesai = new Carbon($item->tanggal_selesai);
            $item->tanggal_selesai = $tanggal_selesai->day . ' ' . $tanggal_selesai->locale('ID')->getTranslatedMonthName() . ' ' . $tanggal_selesai->year;

            return $item;
        });


        return view('dashboard.admin.halaman.laporan.cetak.laporan.promo', [
            'promo' => $promo,
            'filter' => $filter
        ]);
    }

    public function grafikPenjualan()
    {
        if (request()->get('kuartal') && request()->get('tahun')) {
            $filter = [
                'kuartal' => '',
                'tahun' => request()->get('tahun')
            ];

            $chart = new PenjualanRumahChart;
            $query = PembayaranUangMuka::whereYear('tanggal', request()->get('tahun'))->orderBy('tanggal');

            if (request()->get('kuartal') == 1) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 1)->whereMonth('tanggal', '<=', 3);
                });
                $chart->labels(['Januari', 'Februari', 'Maret']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Januari, Februari, Maret)';
            }

            if (request()->get('kuartal') == 2) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 4)->whereMonth('tanggal', '<=', 6);
                });
                $chart->labels(['April', 'Mei', 'Juni']);
                $filter['kuartal'] = request()->get('kuartal') . ' (April, Mei, Juni)';
            }

            if (request()->get('kuartal') == 3) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 7)->whereMonth('tanggal', '<=', 9);
                });
                $chart->labels(['Juli', 'Agustus', 'September']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Juli, Agustus, September)';
            }

            if (request()->get('kuartal') == 4) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 10)->whereMonth('tanggal', '<=', 12);
                });
                $chart->labels(['Oktober', 'November', 'Desember']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Oktober, November, Desember)';
            }

            $dataset = [0, 0, 0];
            foreach ($query->get() as $value) {
                if (in_array(explode('-', $value['tanggal'])[1], [1, 4, 7, 10]))
                    $dataset[0]++;

                if (in_array(explode('-', $value['tanggal'])[1], [2, 5, 8, 11]))
                    $dataset[1]++;

                if (in_array(explode('-', $value['tanggal'])[1], [3, 6, 9, 12]))
                    $dataset[2]++;
            }

            $chart->dataset('Penjualan', 'bar', $dataset)->options([
                'backgroundColor' => '#204A40',
            ]);
            $chart->setStepSize(max($dataset), 8);

            return view('dashboard.admin.halaman.laporan.cetak.laporan.grafik_penjualan', [
                'chart' => $chart,
                'filter' => $filter
            ]);
        }
    }

    public function grafikPemesanan()
    {
        if (request()->get('kuartal') && request()->get('tahun')) {
            $filter = [
                'kuartal' => '',
                'tahun' => request()->get('tahun')
            ];

            $chart = new PenjualanRumahChart;
            $query = PendaftaranPemesanan::whereYear('tanggal', request()->get('tahun'))->orderBy('tanggal');

            if (request()->get('kuartal') == 1) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 1)->whereMonth('tanggal', '<=', 3);
                });
                $chart->labels(['Januari', 'Februari', 'Maret']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Januari, Februari, Maret)';
            }

            if (request()->get('kuartal') == 2) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 4)->whereMonth('tanggal', '<=', 6);
                });
                $chart->labels(['April', 'Mei', 'Juni']);
                $filter['kuartal'] = request()->get('kuartal') . ' (April, Mei, Juni)';
            }

            if (request()->get('kuartal') == 3) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 7)->whereMonth('tanggal', '<=', 9);
                });
                $chart->labels(['Juli', 'Agustus', 'September']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Juli, Agustus, September)';
            }

            if (request()->get('kuartal') == 4) {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 10)->whereMonth('tanggal', '<=', 12);
                });
                $chart->labels(['Oktober', 'November', 'Desember']);
                $filter['kuartal'] = request()->get('kuartal') . ' (Oktober, November, Desember)';
            }

            $dataset = [0, 0, 0];
            foreach ($query->get() as $value) {
                if (in_array(explode('-', $value['tanggal'])[1], [1, 4, 7, 10]))
                    $dataset[0]++;

                if (in_array(explode('-', $value['tanggal'])[1], [2, 5, 8, 11]))
                    $dataset[1]++;

                if (in_array(explode('-', $value['tanggal'])[1], [3, 6, 9, 12]))
                    $dataset[2]++;
            }

            $chart->dataset('Penjualan', 'bar', $dataset)->options([
                'backgroundColor' => '#204A40',
            ]);
            $chart->setStepSize(max($dataset), 8);

            return view('dashboard.admin.halaman.laporan.cetak.laporan.grafik_pemesanan', [
                'chart' => $chart,
                'filter' => $filter
            ]);
        }
    }

    public function keuanganPembayaranRumah()
    {
        $pembayaranUangMuka = PembayaranUangMuka::orderBy('created_at', 'DESC');
        $pendaftaranPemesanan = PendaftaranPemesanan::orderBy('created_at', 'DESC');
        $pembayaran = Pembayaran::where('status', 3)->orderBy('created_at', 'DESC');
        $filter = [
            'dari_tanggal' => '',
            'sampai_tanggal' => ''
        ];

        if (!empty(request()->get('dari_tanggal')) && !empty(request()->get('sampai_tanggal'))) {
            $pembayaranUangMuka = $pembayaranUangMuka->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });
            $pendaftaranPemesanan = $pendaftaranPemesanan->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });
            $pembayaran = $pembayaran->where(function ($q) {
                $q->where('tanggal', '>=', request()->get('dari_tanggal'))
                    ->where('tanggal', '<=', request()->get('sampai_tanggal'));
            });

            $dari_tanggal = new Carbon(request()->get('dari_tanggal'));
            $sampai_tanggal = new Carbon(request()->get('sampai_tanggal'));
            $filter['dari_tanggal'] = $dari_tanggal->day . ' ' . $dari_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $dari_tanggal->year;
            $filter['sampai_tanggal'] = $sampai_tanggal->day . ' ' . $sampai_tanggal->locale('ID')->getTranslatedMonthName() . ' ' . $sampai_tanggal->year;
        }
        $keuanganPembayaranRumah = collect();
        $keuanganPembayaranRumah = $keuanganPembayaranRumah->merge($pembayaranUangMuka->get());
        $keuanganPembayaranRumah = $keuanganPembayaranRumah->merge($pendaftaranPemesanan->get());
        $keuanganPembayaranRumah = $keuanganPembayaranRumah->merge($pembayaran->get());

        $keuanganPembayaranRumah = $keuanganPembayaranRumah->map(function ($item) {
            $data = [
                'tanggal'       => $item->tanggal(),
                'nama_blok'     => $item->rumahPelanggan->rumah->blokPerumahan->nama,
                'nomor_rumah'   => $item->rumahPelanggan->rumah->nomor_rumah,
                'nominal'       => $item->nominal
            ];

            if (get_class($item) == get_class(new PembayaranUangMuka)) {
                if ($item->rumahPelanggan->jenis_pembayaran == 1) {
                    $data['jenis_pembayaran']   = "Tunai";
                    $data['nominal']            = $item->rumahPelanggan->harga_penjualan;
                } else
                    $data['jenis_pembayaran']   = "Uang Muka";
            }

            if (get_class($item) == get_class(new PendaftaranPemesanan))
                $data['jenis_pembayaran'] = "Pemesanan";

            if (get_class($item) == get_class(new Pembayaran))
                $data['jenis_pembayaran'] = "Tunai Berkala";

            return $data;
        });

        return view('dashboard.admin.halaman.laporan.cetak.laporan.keuangan_pembayaran_rumah', [
            'keuanganPembayaranRumah' => $keuanganPembayaranRumah,
            'filter' => $filter
        ]);
    }

    public function rekapTagihanPembayaranRumah()
    {
        $rumahPelanggan = RumahPelanggan::where('jenis_pembayaran', 2)->orWhere('jenis_pembayaran', 1);
        $filter = [
            'blok_perumahan' => '',
            'nomor_rumah' => ''
        ];

        if (!empty(request()->get('id_rumah'))) {
            $rumahPelanggan = $rumahPelanggan->whereHas('rumah', function ($q) {
                $q->where('id', request()->get('id_rumah'));
            })->first();

            if ($rumahPelanggan->jenis_pembayaran == 1) {
                $pembayaran = [
                    [
                        'tanggal' => $rumahPelanggan->pembayaranUangMuka->tanggal(),
                        'tanggal_date_string' => $rumahPelanggan->pembayaranUangMuka->tanggal,
                        'nominal' => $rumahPelanggan->harga_penjualan,
                        'status' => '3'
                    ]
                ];
            }

            if ($rumahPelanggan->jenis_pembayaran == 2) {
                $pembayaran = $rumahPelanggan->pembayaran->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal(),
                        'tanggal_date_string' => $item->tanggal,
                        'nominal' => $item->nominal,
                        'status' => $item->status
                    ];
                });
            }

            $filter['blok_perumahan'] = Rumah::find(request()->get('id_rumah'))->blokPerumahan->nama;
            $filter['nomor_rumah'] = Rumah::find(request()->get('id_rumah'))->nomor_rumah;
        } else
            $pembayaran = collect();

        return view('dashboard.admin.halaman.laporan.cetak.laporan.rekap_tagihan_pembayaran_rumah', [
            'hari_ini'      => Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->toDateString(),
            'pembayaran'    => $pembayaran,
            'filter'        => $filter
        ]);
    }
}
