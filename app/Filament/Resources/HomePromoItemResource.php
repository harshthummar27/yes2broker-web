<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePromoItemResource\Pages;
use App\Models\HomePromoItem;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomePromoItemResource extends Resource
{
    protected static ?string $model = HomePromoItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Promos';

    protected static ?string $modelLabel = 'Promo';

    protected static ?string $pluralModelLabel = 'Promos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promo Setup')
                    ->description('Homepage promos appear below trending listings. All Properties promos appear after every 6 property cards (each slot shows the next promo in sort order).')
                    ->schema([
                        Forms\Components\Select::make('placement')
                            ->label('Show On')
                            ->options([
                                HomePromoItem::PLACEMENT_HOME => 'Homepage only',
                                HomePromoItem::PLACEMENT_PROPERTIES => 'All Properties page only (after every 6 listings)',
                            ])
                            ->default(HomePromoItem::PLACEMENT_HOME)
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                HomePromoItem::TYPE_BANNER => 'Banner (upload image + link)',
                                HomePromoItem::TYPE_PROPERTY => 'Property (select listing + slogan + link)',
                            ])
                            ->required()
                            ->live()
                            ->native(false),
                    ]),
                Forms\Components\Section::make('Banner')
                    ->description('Upload a banner image. The full image is shown and is clickable (URL or popup form).')
                    ->visible(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_BANNER)
                    ->schema([
                        Forms\Components\FileUpload::make('banner_image')
                            ->label('Banner Image')
                            ->image()
                            ->disk('public')
                            ->directory('home-promos')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(8192)
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_BANNER)
                            ->helperText('Recommended: wide image (e.g. 1200×300 px or similar).')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('link_action')
                            ->label('On Click')
                            ->options([
                                HomePromoItem::LINK_ACTION_URL => 'Open link (URL)',
                                HomePromoItem::LINK_ACTION_FORM => 'Show popup form',
                            ])
                            ->default(HomePromoItem::LINK_ACTION_URL)
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_BANNER)
                            ->live()
                            ->native(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('link_url')
                            ->label('Click Link')
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_BANNER
                                && $get('link_action') !== HomePromoItem::LINK_ACTION_FORM)
                            ->visible(fn (Get $get): bool => $get('link_action') !== HomePromoItem::LINK_ACTION_FORM)
                            ->placeholder('/all-properties or https://example.com')
                            ->helperText('Where visitors go when they click the banner.')
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('form_title')
                            ->label('Popup Form Title')
                            ->visible(fn (Get $get): bool => $get('link_action') === HomePromoItem::LINK_ACTION_FORM)
                            ->default('Get in Touch')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Property')
                    ->visible(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_PROPERTY)
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label('Property')
                            ->options(fn (): array => Property::query()
                                ->active()
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_PROPERTY)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slogan')
                            ->label('Slogan')
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_PROPERTY)
                            ->maxLength(255)
                            ->placeholder('Premium 3 BHK starting at ₹78 Lakhs')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('link_url')
                            ->label('Link URL (optional)')
                            ->url()
                            ->placeholder('Leave empty to link to the property detail page')
                            ->helperText('Override the default property page link if needed.')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Display')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('button_text')
                            ->label('Button Text')
                            ->default('Explore More')
                            ->maxLength(50)
                            ->required(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_PROPERTY)
                            ->visible(fn (Get $get): bool => $get('type') === HomePromoItem::TYPE_PROPERTY),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('On All Properties: promo #1 after 6th card, promo #2 after 12th, promo #3 after 18th, etc.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_image')
                    ->label('Preview')
                    ->square()
                    ->getStateUsing(function (HomePromoItem $record): string {
                        try {
                            return (string) $record->imageUrl();
                        } catch (\Throwable $e) {
                            report($e);
                            return '';
                        }
                    }),
                Tables\Columns\TextColumn::make('placement')
                    ->label('Show On')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === HomePromoItem::PLACEMENT_PROPERTIES
                        ? 'All Properties'
                        : 'Homepage')
                    ->color(fn (string $state): string => $state === HomePromoItem::PLACEMENT_PROPERTIES ? 'warning' : 'primary')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => $state === HomePromoItem::TYPE_BANNER ? 'info' : 'success')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('slogan')
                    ->label('Headline / Slogan')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('link_action')
                    ->label('On Click')
                    ->formatStateUsing(fn (?string $state, HomePromoItem $record): string => $record->isBanner() && $record->isFormBanner() ? 'Form' : 'URL')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('link_url')
                    ->label('Link')
                    ->limit(35)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('placement')
                    ->options([
                        HomePromoItem::PLACEMENT_HOME => 'Homepage',
                        HomePromoItem::PLACEMENT_PROPERTIES => 'All Properties',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        HomePromoItem::TYPE_BANNER => 'Banner',
                        HomePromoItem::TYPE_PROPERTY => 'Property',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Promo deleted')
                            ->body('The promo has been deleted.')
                    )
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete promo')
                            ->body('An error occurred while deleting the promo.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Promos deleted')
                                ->body('The selected promos were successfully deleted.')
                        )
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title('Failed to delete promos')
                                ->body('An error occurred while deleting the selected promos.')
                        ),
                ]),
            ])
            ->emptyStateHeading('No promos found')
            ->emptyStateDescription('Create a new promo banner or property highlight.')
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add New Promo')
                    ->icon('heroicon-m-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomePromoItems::route('/'),
            'create' => Pages\CreateHomePromoItem::route('/create'),
            'edit' => Pages\EditHomePromoItem::route('/{record}/edit'),
        ];
    }
}
