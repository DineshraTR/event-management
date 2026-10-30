@include('shared.resource-index', [
    'resourceTitle' => 'Expense categories',
    'resourceRoute' => 'expense-categories',
    'resourceItems' => $categories,
    'createRoute' => 'expense-categories.create',
    'statusFilter' => ['active', 'inactive'],
    'columns' => [
        ['label' => 'Category', 'key' => 'name'],
        ['label' => 'Description', 'key' => 'description'],
        ['label' => 'Linked Expenses', 'key' => 'expenses_count'],
        ['label' => 'Status', 'key' => 'is_active', 'type' => 'boolean']
    ],
    'showRoute' => true,
    'editRoute' => true,
    'deleteRoute' => true
])

