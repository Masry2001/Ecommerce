<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class StatsOverview extends StatsOverviewWidget
{

    protected static ?int $sort = 0;
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $todayRevenue = Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total');

        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        $totalCustomers = Customer::count();
        $todayCustomers = Customer::whereDate('created_at', today())->count();

        $lowStockProducts = Product::lowStock()->count();

        return [
            Stat::make('Total Revenue', number_format($totalRevenue, 2))
                ->description(number_format($todayRevenue, 2) . ' increase today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Orders', $totalOrders)
                ->description($todayOrders . ' orders today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->url(route('filament.admin.resources.orders.index'))
                ->color('warning'),
            Stat::make('Total Customers', $totalCustomers)
                ->description($todayCustomers . ' customers today')
                ->descriptionIcon('heroicon-m-users')
                ->url(route('filament.admin.resources.customers.index'))
                ->color('info'),
            Stat::make('Low Stock Products', $lowStockProducts)
                ->description($lowStockProducts . ' products are low in stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->url(route('filament.admin.resources.products.index'))
                ->color('danger'),
        ];
    }
}
