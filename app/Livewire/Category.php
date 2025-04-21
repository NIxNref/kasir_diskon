<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category as CategoryModel;

class Category extends Component
{
    public $categories, $categoryId, $name;
    public $isEditing = false;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = CategoryModel::withCount('products')->get();
    }

    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->isEditing = false;
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        CategoryModel::create([
            'name' => $this->name,
        ]);

        $this->resetForm();
        $this->loadCategories();
        session()->flash('message', 'Category added successfully!');
    }

    public function editCategory($id)
    {
        $category = CategoryModel::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->isEditing = true;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = CategoryModel::findOrFail($this->categoryId);
        $category->update([
            'name' => $this->name,
        ]);

        $this->resetForm();
        $this->loadCategories();
        session()->flash('message', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = CategoryModel::findOrFail($id);
        $category->delete();

        $this->loadCategories();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function render()
    {
        return view('livewire.category');
    }
}