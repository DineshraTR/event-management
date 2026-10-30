@include('shared.resource-index', [
    'resourceTitle' => 'Event sub-categories',
    'resourceRoute' => 'event-sub-categories',
    'resourceItems' => $subCategories,
    'createRoute' => 'event-sub-categories.create',
    'statusFilter' => ['active', 'inactive'],
    'columns' => [
        ['label' => 'Sub-category', 'key' => 'name'],
        ['label' => 'Category', 'key' => 'category.name'],
        ['label' => 'Description', 'key' => 'description'],
        ['label' => 'Cost', 'key' => 'cost_price', 'type' => 'money'],
        ['label' => 'Sell price', 'key' => 'sell_price', 'type' => 'money'],
        ['label' => 'Status', 'key' => 'is_active', 'type' => 'boolean']
    ],
    'showRoute' => true,
    'editRoute' => true,
    'deleteRoute' => true
])