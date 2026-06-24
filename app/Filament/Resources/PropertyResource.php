<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Services\LookupOptionService;
use App\Support\MapEmbed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?string $modelLabel = 'Property';

    protected static ?string $pluralModelLabel = 'Properties';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('location')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bhk')
                            ->label('Configuration / BHK')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('area')
                            ->label('Project Area')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('possession')
                            ->label('Possession Date')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->label('Price Range')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('city')
                            ->options(fn (): array => app(LookupOptionService::class)->citiesForAdmin())
                            ->default(fn (): ?string => array_key_first(app(LookupOptionService::class)->citiesForAdmin()))
                            ->searchable(),
                        Forms\Components\Select::make('property_type')
                            ->label('Property Type')
                            ->options(fn (): array => app(LookupOptionService::class)->propertyTypesForAdmin())
                            ->searchable()
                            ->native(false),
                    ]),
                Forms\Components\Section::make('Media')
                    ->description('Upload images or paste an external image URL.')
                    ->schema([
                        Forms\Components\FileUpload::make('image_upload')
                            ->label('Upload Featured Image')
                            ->image()
                            ->disk('public')
                            ->directory('properties')
                            ->visibility('public')
                            ->maxSize(8192)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('image')
                            ->label('Featured Image URL')
                            ->url()
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('Used when no file is uploaded above.')
                            ->required(fn (Forms\Get $get): bool => blank($get('image_upload')))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('gallery_uploads')
                            ->label('Upload Gallery Images')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('properties/gallery')
                            ->visibility('public')
                            ->maxSize(8192)
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('gallery')
                            ->label('Gallery Image URLs')
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label('Image URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, ?array $state): void {
                                if (empty($state)) {
                                    return;
                                }

                                if (isset($state[0]['url'])) {
                                    return;
                                }

                                $component->state(array_map(
                                    fn (string $url) => ['url' => $url],
                                    $state
                                ));
                            })
                            ->dehydrateStateUsing(function (?array $state): array {
                                if (empty($state)) {
                                    return [];
                                }

                                return array_values(array_filter(array_map(
                                    fn (array $item) => $item['url'] ?? null,
                                    $state
                                )));
                            }),
                    ]),
                Forms\Components\Section::make('Detail Page Content')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\Fieldset::make('Overview')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('overview.project_area')
                                    ->label('Project Area'),
                                Forms\Components\TextInput::make('overview.configurations')
                                    ->label('Configurations & Sizes'),
                                Forms\Components\TextInput::make('overview.project_size')
                                    ->label('Project Size'),
                                Forms\Components\TextInput::make('overview.launch_date')
                                    ->label('Launch Date'),
                                Forms\Components\TextInput::make('overview.price_range')
                                    ->label('Price Range'),
                                Forms\Components\TextInput::make('overview.possession')
                                    ->label('Possession Date'),
                                Forms\Components\Textarea::make('overview.rera_id')
                                    ->label('RERA ID')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\TagsInput::make('amenities')
                            ->placeholder('Add amenity')
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('faqs')
                            ->schema([
                                Forms\Components\TextInput::make('question')
                                    ->required(),
                                Forms\Components\Textarea::make('answer')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->defaultItems(0)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('map_embed_url')
                            ->label('Google Maps Embed')
                            ->rows(4)
                            ->helperText('Paste the full <iframe> code from Google Maps → Share → Embed a map, or paste only the embed URL.')
                            ->dehydrateStateUsing(fn (?string $state): ?string => MapEmbed::normalizeEmbedInput($state))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('street_view_embed_url')
                            ->label('360° / Street View Embed')
                            ->rows(4)
                            ->helperText('Paste the full <iframe> code or only the Street View embed URL.')
                            ->dehydrateStateUsing(fn (?string $state): ?string => MapEmbed::normalizeEmbedInput($state))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('brochure_url')
                            ->label('Brochure PDF URL')
                            ->url()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Settings')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Show New Badge')
                            ->default(true),
                        Forms\Components\Toggle::make('is_trending')
                            ->label('Trending on Homepage')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->getStateUsing(fn (Property $record): string => $record->image_url),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->limit(35)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('property_type')
                    ->label('Type')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_new')
                    ->label('New')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('title')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published'),
                Tables\Filters\SelectFilter::make('city')
                    ->options(fn (): array => app(LookupOptionService::class)->citiesForAdmin()),
                Tables\Filters\SelectFilter::make('property_type')
                    ->label('Type')
                    ->options(fn (): array => app(LookupOptionService::class)->propertyTypesForAdmin()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
