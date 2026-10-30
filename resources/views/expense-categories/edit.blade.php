@include('shared.resource-form', [
    'formTitle' => 'Edit expense category',
    'resourceRoute' => 'expense-categories',
    'formAction' => route('expense-categories.update', $expenseCategory),
    'formMethod' => 'PUT',
    'record' => $expenseCategory,
    'fields' => [
        ['name' => 'name', 'label' => 'Category name', 'required' => true],
        ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'width' => 12],
        ['name' => 'is_active', 'label' => 'Status', 'type' => 'checkbox', 'value' => true, 'checkLabel' => 'Active']
    ]
])

