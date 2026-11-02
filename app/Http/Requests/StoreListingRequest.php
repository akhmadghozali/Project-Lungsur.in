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
        if (! Auth::check() || ! Auth::user()->isSeller()) {
            return false;
        }

        if ($this->route('listing') !== null) {
            $listing = $this->route('listing') instanceof \App\Models\Listing
                ? $this->route('listing')
                : \App\Models\Listing::find($this->route('listing'));

            return $listing && (int) $listing->user_id === (int) Auth::id();
        }

        return true;
    }

    /**
     * Tangani kegagalan otorisasi dengan redirect ramah ke dasbor.
     */
    protected function failedAuthorization()
    {
        if (Auth::check() && ! Auth::user()->isSeller()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('dashboard')->with(
                    'warning',
                    'Akun dalam Mode Pembeli tidak dapat mempublikasikan atau mengelola iklan barang dagangan. Silakan beralih ke Mode Penjual terlebih dahulu.'
                )
            );
        }

        abort(403, 'Anda tidak memiliki hak untuk melakukan aksi ini.');
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

        $rules = [
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
        ];

        if ($isUpdate) {
            $listing = $this->route('listing') instanceof \App\Models\Listing
                ? $this->route('listing')
                : \App\Models\Listing::find($this->route('listing'));

            $rules['deleted_image_ids'] = ['nullable', 'array'];
            $rules['deleted_image_ids.*'] = [
                'integer',
                function (string $attribute, mixed $value, \Closure $fail) use ($listing): void {
                    if (! $listing || ! $listing->images()->where('id', $value)->exists()) {
                        $fail('Foto yang ingin dihapus tidak valid untuk iklan ini.');
                    }
                },
            ];

            $rules['images'] = ['nullable', 'array'];
            $rules['images.*'] = ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
        } else {
            $rules['images'] = ['required', 'array', 'min:1', 'max:5'];
            $rules['images.*'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
        }

        return $rules;
    }

    /**
     * Tambahkan validasi kustom setelah aturan utama dijalankan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('listing') !== null;

            if ($isUpdate) {
                $listing = $this->route('listing') instanceof \App\Models\Listing
                    ? $this->route('listing')
                    : \App\Models\Listing::find($this->route('listing'));

                if ($listing) {
                    $deletedIds = array_filter((array) $this->input('deleted_image_ids', []));
                    $existingCount = $listing->images()->count();
                    $validDeletedCount = $listing->images()->whereIn('id', $deletedIds)->count();
                    $retainedCount = max(0, $existingCount - $validDeletedCount);

                    $newFiles = $this->file('images');
                    $newCount = is_array($newFiles) ? count($newFiles) : 0;
                    $total = $retainedCount + $newCount;

                    if ($total > 5) {
                        $validator->errors()->add('images', 'Unggah maksimal 5 foto.');
                    } elseif ($total < 1) {
                        $validator->errors()->add('images', 'Iklan wajib memiliki minimal 1 foto barang.');
                    }
                }
            }
        });
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

            'images.required'      => 'Foto barang wajib diunggah minimal 1 foto.',
            'images.array'         => 'Format data foto tidak valid.',
            'images.min'           => 'Foto barang wajib diunggah minimal 1 foto.',
            'images.max'           => 'Unggah maksimal 5 foto.',
            'images.*.required'    => 'Foto barang wajib diunggah.',
            'images.*.image'       => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes'       => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'images.*.max'         => 'Ukuran setiap foto maksimal 5 MB.',
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
            'images'      => 'foto barang',
            'images.*'    => 'foto barang',
        ];
    }
}
