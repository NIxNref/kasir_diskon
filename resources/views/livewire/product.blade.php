<div class="container">
    <div class="row my-2">
        <div class="col-12">
            <button wire:click="pilihMenu('lihat')"
                class="btn {{ $pilihanMenu == 'lihat' ? 'btn-primary' : 'btn-outline-primary' }}">
                All Products
            </button>
            <button wire:click="pilihMenu('tambah')"
                class="btn {{ $pilihanMenu == 'tambah' ? 'btn-primary' : 'btn-outline-primary' }}">
                Add Product
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
                                <th>Code</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Image</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach ($semuaProduk as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->product_code }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price, 2, ',', '.') }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image"
                                                    class="img-thumbnail" width="50">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="pilihEdit({{ $product->id }})"
                                                class="btn btn-outline-primary">Edit</button>
                                            <button wire:click="pilihHapus({{ $product->id }})"
                                                class="btn btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                <div class="card border-primary">
                    <div class="card-header">{{ $pilihanMenu == 'tambah' ? 'Add Product' : 'Edit Product' }}</div>
                    <div class="card-body">
                        <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'tambahProduct' : 'updateProduct' }}">
                            <input type="hidden" wire:model="productId">

                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" wire:model="image">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-thumbnail mt-2"
                                        width="150">
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="product_code" class="form-label">Code</label>
                                <input type="text" class="form-control" wire:model="product_code">
                                @error('product_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" wire:model="price">
                                @error('price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" wire:model="stock">
                                @error('stock')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control" wire:model="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit"
                                class="btn btn-primary">{{ $pilihanMenu == 'tambah' ? 'Save' : 'Save Changes' }}</button>
                            <button wire:click="pilihMenu('lihat')" class="btn btn-secondary">Cancel</button>
                        </form>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'hapus')
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">Delete Product</div>
                    <div class="card-body">
                        <p>Are you sure you want to delete this product?</p>
                        <p>Name: <strong>{{ $produkTerpilih->name }}</strong></p>
                        <button wire:click="hapusProduct" class="btn btn-danger">Yes</button>
                        <button wire:click="pilihMenu('lihat')" class="btn btn-outline-danger">No</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
