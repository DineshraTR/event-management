@include('shared.resource-form', [
    'formTitle' => 'Add expense category',
    'resourceRoute' => 'expense-categories',
    'formAction' => route('expense-categories.store'),
    'fields' => [
        ['name' => 'name', 'label' => 'Category name', 'required' => true],
        ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'width' => 12],
        ['name' => 'is_active', 'label' => 'Status', 'type' => 'checkbox', 'value' => true, 'checkLabel' => 'Active']
    ]
])

