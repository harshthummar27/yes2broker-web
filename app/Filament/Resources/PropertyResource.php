<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Services\LookupOptionService;
use App\Support\IndianPrice;
use App\Support\MapEmbed;
use App\Support\ProjectAreaUnit;
use App\Support\PropertyOverview;
use App\Support\PropertyUnitConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
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
                        Forms\Components\Fieldset::make('Location')
                            ->schema([
                                Forms\Components\TextInput::make('address_line_1')
                                    ->label('Address Line 1')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('address_line_2')
                                    ->label('Address Line 2')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('city')
                                    ->options(fn (): array => app(LookupOptionService::class)->citiesForAdmin())
                                    ->default(fn (): string => app(LookupOptionService::class)->defaultCityName())
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('locality', null)),
                                Forms\Components\Select::make('locality')
                                    ->options(fn (Forms\Get $get): array => app(LookupOptionService::class)->localitiesForAdmin($get('city')))
                                    ->searchable()
                                    ->native(false),
                                Forms\Components\Select::make('state')
                                    ->options(fn (): array => app(LookupOptionService::class)->statesForAdmin())
                                    ->default('Gujarat')
                                    ->required()
                                    ->searchable()
                                    ->native(false),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('project_area_value')
                            ->label('Project Area')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.0001)
                            ->live(debounce: 300),
                        Forms\Components\Select::make('project_area_unit')
                            ->label('Project Unit')
                            ->options(fn (): array => app(LookupOptionService::class)->projectUnitsForAdmin())
                            ->default(fn (): string => app(LookupOptionService::class)->defaultProjectUnitName())
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get, ?string $old): void {
                                if (blank($state) || blank($old) || $state === $old) {
                                    return;
                                }

                                $value = $get('project_area_value');

                                if ($value === null || $value === '') {
                                    return;
                                }

                                $converted = ProjectAreaUnit::convert((float) $value, $old, $state);
                                $set('project_area_value', ProjectAreaUnit::formatValue($converted));
                            }),
                        Forms\Components\Placeholder::make('area_preview')
                            ->label('Calculated project area')
                            ->content(function (Forms\Get $get): string {
                                $formatted = Property::formatProjectArea(
                                    $get('project_area_value'),
                                    $get('project_area_unit')
                                );

                                return filled($formatted) ? $formatted : '—';
                            })
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('possession_is_ready')
                            ->label('Ready to Move')
                            ->default(false)
                            ->live(),
                        Forms\Components\DatePicker::make('possession_date')
                            ->label('Possession Date')
                            ->required(fn (Forms\Get $get): bool => ! $get('possession_is_ready'))
                            ->hidden(fn (Forms\Get $get): bool => (bool) $get('possession_is_ready'))
                            ->native(false)
                            ->displayFormat('F Y')
                            ->format('Y-m-d')
                            ->closeOnDateSelection()
                            ->dehydrateStateUsing(function (?string $state): ?string {
                                if (blank($state)) {
                                    return null;
                                }

                                return \Carbon\Carbon::parse($state)->startOfMonth()->format('Y-m-d');
                            }),
                        Forms\Components\DatePicker::make('overview.launch_date')
                            ->label('Launch Date')
                            ->native(false)
                            ->displayFormat('F Y')
                            ->format('Y-m-d')
                            ->closeOnDateSelection()
                            ->live()
                            ->dehydrateStateUsing(function (?string $state): ?string {
                                if (blank($state)) {
                                    return null;
                                }

                                return Property::formatMonthYear($state);
                            }),
                        Forms\Components\TextInput::make('overview.rera_id')
                            ->label('RERA ID')
                            ->live(),
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
                            ->schema([
                                Forms\Components\Repeater::make('overview.unit_configurations')
                                    ->label('Configurations, Sizes & Units')
                                    ->helperText('Add one row per configuration (required). Example: 1 BHK Apartment — 4000 Sq. Ft. — 80 units available.')
                                    ->live()
                                    ->minItems(1)
                                    ->afterStateHydrated(function (Forms\Components\Repeater $component, ?array $state): void {
                                        if (empty($state)) {
                                            return;
                                        }

                                        $newState = [];
                                        foreach ($state as $item) {
                                            if (isset($item['bhk']) || isset($item['type'])) {
                                                $newState[] = $item;
                                                continue;
                                            }

                                            $config = $item['configuration'] ?? '';
                                            $parsed = self::splitConfiguration($config);
                                            
                                            $item['bhk'] = $parsed['bhk'];
                                            $item['type'] = $parsed['type'];
                                            $newState[] = $item;
                                        }

                                        $component->state($newState);
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('bhk')
                                            ->label('BHK')
                                            ->datalist([
                                                '1 BHK',
                                                '2 BHK',
                                                '3 BHK',
                                                '4 BHK',
                                                '5 BHK',
                                                '1 & 2 BHK',
                                                '2 & 3 BHK',
                                                '3 & 4 BHK',
                                                '4 & 5 BHK',
                                                '3 BHK & 4 BHK',
                                            ])
                                            ->required(fn (Forms\Get $get): bool => blank($get('type')))
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::updateConfiguration($set, $get)),
                                        Forms\Components\TextInput::make('type')
                                            ->label('Type')
                                            ->datalist(fn (): array => array_values(app(LookupOptionService::class)->propertyTypesForAdmin()))
                                            ->required(fn (Forms\Get $get): bool => blank($get('bhk')))
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => self::updateConfiguration($set, $get)),
                                        Forms\Components\Hidden::make('configuration'),
                                        Forms\Components\TextInput::make('size_value')
                                            ->label('Size')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->required(),
                                        Forms\Components\Select::make('size_unit')
                                            ->label('Size Unit')
                                            ->options(fn (): array => app(LookupOptionService::class)->projectUnitsForAdmin())
                                            ->default('Sq. Ft.')
                                            ->native(false)
                                            ->required(),
                                        Forms\Components\TextInput::make('total_units')
                                            ->label('Total Units')
                                            ->numeric()
                                            ->minValue(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get): void {
                                                $available = $get('available_units');

                                                if (filled($state) && filled($available) && (int) $available > (int) $state) {
                                                    $set('available_units', (int) $state);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('available_units')
                                            ->label('Available Units')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(fn (Forms\Get $get): ?int => filled($get('total_units'))
                                                ? (int) $get('total_units')
                                                : null)
                                            ->helperText(fn (Forms\Get $get): ?string => filled($get('total_units'))
                                                ? 'Maximum '.$get('total_units').' (total units).'
                                                : null)
                                            ->rule(function (Forms\Get $get): array {
                                                if (! filled($get('total_units'))) {
                                                    return ['nullable', 'integer', 'min:0'];
                                                }

                                                return ['nullable', 'integer', 'min:0', 'max:'.(int) $get('total_units')];
                                            }),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Price (₹)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->placeholder('e.g. 1000000 for 10 Lakh')
                                            ->helperText('Optional. Numeric value in Rupees.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->itemLabel(function (array $state): ?string {
                                        if (blank($state['configuration'] ?? null)) {
                                            return null;
                                        }

                                        $label = $state['configuration'];

                                        $size = filled($state['size_value'] ?? null)
                                            ? trim($state['size_value'].' '.($state['size_unit'] ?? ''))
                                            : null;

                                        if ($size) {
                                            $label .= ' — '.$size;
                                        }

                                        if (filled($state['price'] ?? null)) {
                                            $label .= ' — '.IndianPrice::formatPart($state['price']);
                                        }

                                        return $label;
                                    })
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('overview_listing_units_table')
                                    ->label('Configuration data table')
                                    ->content(function (Forms\Get $get): HtmlString {
                                        $items = PropertyUnitConfiguration::normalizeList(
                                            is_array($get('overview.unit_configurations')) ? $get('overview.unit_configurations') : []
                                        );

                                        return new HtmlString(view('filament.forms.property-listing-units-table', [
                                            'rows' => PropertyOverview::configurationTableRows($items),
                                        ])->render());
                                    })
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('overview.project_size')
                                    ->label('Project Size')
                                    ->helperText('Example: 1 Building – 100 units')
                                    ->visible(function (Forms\Get $get): bool {
                                        $items = PropertyUnitConfiguration::normalizeList(
                                            is_array($get('overview.unit_configurations')) ? $get('overview.unit_configurations') : []
                                        );

                                        return $items === [];
                                    }),
                            ]),
                        Forms\Components\CheckboxList::make('amenities')
                            ->label('Amenities')
                            ->options(fn (): array => app(LookupOptionService::class)->amenityOptionsForAdmin())
                            ->columns(2)
                            ->bulkToggleable()
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
                        Forms\Components\Toggle::make('show_street_view')
                            ->label('Show 360° / Street View section')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('street_view_embed_url')
                            ->label('360° / Street View Embed (optional)')
                            ->rows(4)
                            ->helperText('Optional. Leave empty to auto-generate from the property location when enabled above.')
                            ->dehydrateStateUsing(fn (?string $state): ?string => MapEmbed::normalizeEmbedInput($state))
                            ->visible(fn (Forms\Get $get): bool => (bool) $get('show_street_view'))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('brochure_upload')
                            ->label('Brochure PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('properties/brochures')
                            ->visibility('public')
                            ->maxSize(12288)
                            ->helperText('Optional. If no PDF is uploaded, the Download Brochure button opens the inquiry popup on the website.')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Settings')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Show New Project badge')
                            ->helperText('Enable only for newly added listings.')
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
                Tables\Columns\TextColumn::make('locality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('location')
                    ->label('Full address')
                    ->getStateUsing(fn (Property $record): string => $record->displayLocation())
                    ->searchable(['address_line_1', 'address_line_2', 'locality', 'location'])
                    ->limit(35)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bhk')
                    ->label('Configuration')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Project Area')
                    ->searchable()
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

    public static function splitConfiguration(string $config): array
    {
        $config = trim($config);
        
        if (preg_match('/^(.+?\bBHKs?\b)(.*)$/ui', $config, $matches)) {
            return [
                'bhk' => trim($matches[1]),
                'type' => trim($matches[2]),
            ];
        }

        return [
            'bhk' => '',
            'type' => $config,
        ];
    }

    public static function updateConfiguration(Forms\Set $set, Forms\Get $get): void
    {
        $bhk = $get('bhk');
        $type = $get('type');
        
        $set('configuration', trim(implode(' ', array_filter([$bhk, $type]))));
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
