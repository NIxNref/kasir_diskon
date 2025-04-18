<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User as ModelUser;

class User extends Component
{
    public $pilihanMenu = 'lihat';
    public $userId, $name, $email, $role, $password;
    public $penggunaTerpilih;

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->role = null;
        $this->password = '';
    }

    public function simpanUser()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required',
            'password' => 'required'
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'role.required' => 'Role harus diisi',
            'password.required' => 'Password harus diisi'
        ]);

        if ($this->userId) {
            $user = ModelUser::find($this->userId);
            if ($user) {
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $this->role,
                    'password' => bcrypt($this->password)
                ]);
            }
        } else {
            // Cek apakah email sudah ada sebelum insert
            if (ModelUser::where('email', $this->email)->exists()) {
                session()->flash('error', 'Email sudah terdaftar');
                return;
            }

            ModelUser::create([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'password' => bcrypt($this->password)
            ]);
        }
        
        $this->resetForm();
        $this->pilihMenu('lihat');
    }

    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelUser::findOrfail($id);
        $this->pilihanMenu = 'hapus';
        
    }

    public function deleteUser()
    {
        if ($this->penggunaTerpilih) {
            $this->penggunaTerpilih->isDeleted = 1; // Set isDeleted to true
            $this->penggunaTerpilih->save(); // Save the changes to the database
            $this->pilihMenu('lihat');
        }
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }

    public function pilihEdit($id)
    {
        $user = ModelUser::find($id);
        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->pilihanMenu = 'edit';
        }
    }

    public function simpanEdit()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'role.required' => 'Role harus diisi',
        ]);

        if ($this->userId) {
            $user = ModelUser::find($this->userId);
            if ($user) {
                // Perbarui hanya jika password diubah
                $data = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $this->role,
                ];

                if (!empty($this->password)) {
                    $data['password'] = bcrypt($this->password);
                }

                $user->fill($data)->save();
            }
        }
        
        $this->resetForm();
        $this->pilihMenu('lihat');
    }


    public function render()
    {
        return view('livewire.user')->with([
            'semuaPengguna' => ModelUser::where('isDeleted', 0)->get()
        ]);
    }
}
