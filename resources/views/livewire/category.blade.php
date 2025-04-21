<div class="container">
    <h3>Manage Categories</h3>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'updateCategory' : 'saveCategory' }}">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" class="form-control" wire:model="name">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEditing ? 'Update Category' : 'Add Category' }}
        </button>
        @if ($isEditing)
            <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
        @endif
    </form>

    <h4 class="mt-5">Existing Categories</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Items</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $index => $category)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td> <!-- Display the product count -->
                    <td>
                        <button class="btn btn-outline-primary btn-sm" wire:click="editCategory({{ $category->id }})">
                            Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm" wire:click="deleteCategory({{ $category->id }})">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
