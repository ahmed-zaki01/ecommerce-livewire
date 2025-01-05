<?php

namespace App\Models;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function getForm()
    {
        return [
            Group::make()->schema([
                Section::make('Product Information')->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, string|null $state, Set $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->required()
                        ->dehydrated()
                        ->maxLength(255),

                    MarkdownEditor::make('description')
                        ->required()
                        ->fileAttachmentsDirectory('products')
                        ->columnSpanFull(),

                    Section::make('images')->schema([
                        FileUpload::make('images')
                            ->required()
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(5)
                            ->reorderable(),
                    ]),

                ]),
            ])->columnSpan(2),

            Group::make()->schema([
                Section::make('Pricing')->schema([
                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                ]),

                Section::make('Associations')->schema([
                    Select::make('category_id')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->relationship('category', 'name'),

                    Select::make('brand_id')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->relationship('brand', 'name'),
                ]),

                Section::make('Status')->schema([
                    Toggle::make('is_active')
                        ->required(),

                    Toggle::make('in_stock')
                        ->required(),

                    Toggle::make('is_featured')
                        ->required(),

                    Toggle::make('on_sale')
                        ->required(),
                ])
            ])->columnSpan(1),
        ];
    }
}
