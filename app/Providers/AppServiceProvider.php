<?php

namespace App\Providers;

use App\Models\Borrowing;
use App\Models\MembershipPlan;
use App\Repositories\Book\BookRepository;
use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Borrowing\BorrowingRepository;
use App\Repositories\Borrowing\BorrowingRepositoryInterface;
use App\Repositories\MembershipPlan\MembershipPlanRepository;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BorrowingRepositoryInterface::class, function ($app) {
            return new BorrowingRepository(
                $app->make(Borrowing::class),
                $app->make(UserRepositoryInterface::class),
                $app->make(BorrowingRepositoryInterface::class),
                $app->make(BookRepositoryInterface::class),
            );
        });
        $this->app->bind(
            MembershipPlanRepositoryInterface::class,
            function ($app) {
                return new MembershipPlanRepository(
                    $app->make(MembershipPlan::class),
                    $app->make(UserRepositoryInterface::class),
                );
            },
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
