<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyConfigurationResource\Pages;
use App\Models\PropertyConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PropertyConfigurationResource extends Resource
{
    protected static ?string $model = PropertyConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Configurations / BHK';

    protected static ?string $modelLabel = 'Configuration';

    protected static ?string $pluralModelLabel = 'Configurations / BHK';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Configuration / BHK')
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
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Configuration / BHK')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Configuration deleted')
                            ->body('The configuration was successfully deleted.')
                    )
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete configuration')
                            ->body('An error occurred while deleting the configuration.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Configurations deleted')
                                ->body('The selected configurations were successfully deleted.')
                        )
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title('Failed to delete configurations')
                                ->body('Some or all selected configurations could not be deleted.')
                        ),
                ]),
            ])
            ->emptyStateHeading('No configurations found')
            ->emptyStateDescription('Create configuration options like 1 BHK, 2 BHK, Villa, Studio, etc.')
            ->emptyStateIcon('heroicon-o-home-modern')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Configuration')
                    ->icon('heroicon-m-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyConfigurations::route('/'),
            'create' => Pages\CreatePropertyConfiguration::route('/create'),
            'edit' => Pages\EditPropertyConfiguration::route('/{record}/edit'),
        ];
    }
}
