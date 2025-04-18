<div class="container">
    <div class="row my-2">
        <div class="col-12">
            <button wire:click="pilihMenu('lihat')"
                class="btn {{ $pilihanMenu == 'lihat' ? 'btn-primary' : 'btn-outline-primary' }}">
                All User
            </button>
            <button wire:click="pilihMenu('tambah')"
                class="btn {{ $pilihanMenu == 'tambah' ? 'btn-primary' : 'btn-outline-primary' }}">
                Add User
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if ($pilihanMenu == 'lihat')
                <div class="card border-primary">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach ($semuaPengguna as $pengguna)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pengguna->name }}</td>
                                        <td>{{ $pengguna->email }}</td>
                                        <td>{{ $pengguna->role }}</td>
                                        <td>
                                            <button wire:click="pilihEdit({{ $pengguna->id }})"
                                                class="btn btn-outline-primary">
                                                Edit
                                            </button>
                                            <buttton wire:click="pilihHapus({{ $pengguna->id }})"
                                                class="btn {{ $pilihanMenu == 'hapus' ? 'btn-danger' : 'btn-outline-danger' }}">
                                                Delete
                                            </buttton>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'tambah')
                <div class="card border-primary">
                    <div class="card-header">Add User</div>
                    <div class="card-body">
                        <form wire:submit.prevent="simpanUser">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" wire:model="password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" wire:model="role">
                                    <option>--Select Role--</option>
                                    <option value="admin">Admin</option>
                                    <option value="kasir">Kasir</option>
                                    <option value="user">User</option>
                                </select>

                                @error('role')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'edit')
                <div class="card border-primary">
                    <div class="card-header">Edit User</div>
                    <div class="card-body">
                        <form wire:submit.prevent="simpanEdit">
                            <input type="hidden" wire:model="userId">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model="name">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" wire:model="password">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" wire:model="role">
                                    <option value="admin">Admin</option>
                                    <option value="kasir">Kasir</option>
                                    <option value="user">User</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button wire:click="pilihMenu('lihat')" class="btn btn-secondary">Cancel</button>
                        </form>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'hapus')
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        Delete User
                    </div>
                    <div class="card-body">
                        <p>Are you sure want to delete this user?</p>
                        <p>Name: <strong>{{ $penggunaTerpilih->name }}</strong></p>
                        <button wire:click="deleteUser" class="btn btn-danger">Yes</button>
                        <button wire:click="pilihMenu('lihat')" class="btn btn-outline-danger">No</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
