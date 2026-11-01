<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreListingRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk melakukan permintaan ini.
     * Hanya pengguna terautentikasi dalam Mode Penjual yang boleh mempublikasikan iklan.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isSeller();
    }

    /**
     * Tangani kegagalan otorisasi dengan redirect ramah ke dasbor.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            redirect()->route('dashboard')->with(
                'warning',
                'Akun dalam Mode Pembeli tidak dapat mempublikasikan iklan barang dagangan. Silakan beralih ke Mode Penjual terlebih dahulu.'
            )
        );
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     * Aturan ini digunakan bersama untuk store (POST) dan update (PUT/PATCH).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('listing') !== null;

        return [
            'title' => ['required', 'string', 'max:200'],

            'price' => [
                'required',
                'numeric',
                'min:1000',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ((int) $value % 1000 !== 0) {
                        $fail('Harga harus dalam kelipatan Rp1.000 (contoh: Rp15.000, Rp20.000).');
                    }
                },
            ],

            'condition' => [
                'required',
                'string',
                'in:baru,bekas_mulus,bekas_baik,bekas_normal,bekas_perlu_perbaikan',
            ],

            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'addresses' => ['required', 'string', 'max:255'],

            'description' => ['required', 'string'],

            'image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2 MB
            ],
        ];
    }

    /**
     * Pesan error kustom berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Judul barang wajib diisi.',
            'title.string'         => 'Judul barang harus berupa teks.',
            'title.max'            => 'Judul barang maksimal 200 karakter.',

            'price.required'       => 'Harga barang wajib diisi.',
            'price.numeric'        => 'Harga harus berupa angka.',
            'price.min'            => 'Harga minimal adalah Rp1.000.',

            'condition.required'   => 'Kondisi barang wajib dipilih.',
            'condition.string'     => 'Kondisi barang tidak valid.',
            'condition.in'         => 'Kondisi barang yang dipilih tidak valid.',

            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.integer'  => 'Kategori barang tidak valid.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',

            'addresses.required'   => 'Lokasi/area kampus wajib diisi.',
            'addresses.string'     => 'Lokasi harus berupa teks.',
            'addresses.max'        => 'Lokasi maksimal 255 karakter.',

            'description.required' => 'Deskripsi barang wajib diisi.',
            'description.string'   => 'Deskripsi harus berupa teks.',

            'image.required'       => 'Foto barang wajib diunggah.',
            'image.image'          => 'File yang diunggah harus berupa gambar.',
            'image.mimes'          => 'Format foto harus JPEG, PNG, JPG, atau WEBP.',
            'image.max'            => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    /**
     * Label kustom atribut form.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'       => 'judul barang',
            'price'       => 'harga barang',
            'condition'   => 'kondisi barang',
            'category_id' => 'kategori barang',
            'addresses'   => 'lokasi/area kampus',
            'description' => 'deskripsi barang',
            'image'       => 'foto barang',
        ];
    }
}
