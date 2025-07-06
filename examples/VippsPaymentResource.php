<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VippsPaymentResource\Pages;
use App\Models\VippsPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Wirement\Vipps\Facades\Vipps;
use Filament\Notifications\Notification;

class VippsPaymentResource extends Resource
{
    protected static ?string $model = VippsPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Vipps Payments';

    protected static ?string $modelLabel = 'Vipps Payment';

    protected static ?string $pluralModelLabel = 'Vipps Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (øre)')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        
                        Forms\Components\Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'NOK' => 'Norwegian Krone',
                                'DKK' => 'Danish Krone',
                                'SEK' => 'Swedish Krona',
                            ])
                            ->default('NOK')
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->maxLength(500),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'created' => 'Created',
                                'authorized' => 'Authorized',
                                'captured' => 'Captured',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Vipps Information')
                    ->schema([
                        Forms\Components\TextInput::make('vipps_order_id')
                            ->label('Vipps Order ID')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('payment_url')
                            ->label('Payment URL')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NOK', divideBy: 100)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'created' => 'blue',
                        'authorized' => 'yellow',
                        'captured' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'created' => 'Created',
                        'authorized' => 'Authorized',
                        'captured' => 'Captured',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),
                
                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'NOK' => 'Norwegian Krone',
                        'DKK' => 'Danish Krone',
                        'SEK' => 'Swedish Krona',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('refresh_status')
                    ->label('Refresh Status')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (VippsPayment $record) {
                        try {
                            $status = Vipps::getPaymentStatus($record->vipps_order_id);
                            $record->update(['status' => strtolower($status['state'] ?? 'unknown')]);
                            
                            Notification::make()
                                ->title('Status Updated')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to refresh status')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (VippsPayment $record) => in_array($record->status, ['created', 'authorized'])),
                
                Tables\Actions\Action::make('capture')
                    ->label('Capture Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (VippsPayment $record) {
                        try {
                            Vipps::capturePayment($record->vipps_order_id, $record->amount);
                            $record->update(['status' => 'captured']);
                            
                            Notification::make()
                                ->title('Payment Captured')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to capture payment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (VippsPayment $record) => $record->status === 'authorized'),
                
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Payment')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (VippsPayment $record) {
                        try {
                            Vipps::cancelPayment($record->vipps_order_id);
                            $record->update(['status' => 'cancelled']);
                            
                            Notification::make()
                                ->title('Payment Cancelled')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to cancel payment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (VippsPayment $record) => in_array($record->status, ['created', 'authorized'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('order_id')
                            ->label('Order ID')
                            ->weight(FontWeight::Bold),
                        
                        TextEntry::make('vipps_order_id')
                            ->label('Vipps Order ID')
                            ->weight(FontWeight::Bold),
                        
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('NOK', divideBy: 100)
                            ->weight(FontWeight::Bold),
                        
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->badge(),
                        
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'created' => 'blue',
                                'authorized' => 'yellow',
                                'captured' => 'success',
                                'cancelled' => 'danger',
                                'refunded' => 'warning',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        
                        TextEntry::make('payment_url')
                            ->label('Payment URL')
                            ->url()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVippsPayments::route('/'),
            'create' => Pages\CreateVippsPayment::route('/create'),
            'view' => Pages\ViewVippsPayment::route('/{record}'),
            'edit' => Pages\EditVippsPayment::route('/{record}/edit'),
        ];
    }
}
