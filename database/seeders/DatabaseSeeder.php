<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Role;
use App\Models\State;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call([]);

        #region Categories
        $categories = [
            [
                'name' => 'Entrantes',
                'slug' => 'entrantes',
                'description' => 'Pequeñas porciones para abrir el apetito.',
                'priority' => '0',
            ],
            [
                'name' => 'Sopas',
                'slug' => 'sopas',
                'description' => 'Variedad de sopas, perfectas para comenzar la comida.',
                'priority' => '1',
            ],
            [
                'name' => 'Platos principales',
                'slug' => 'platos-principales',
                'description' => 'Platos abundantes y completos para el plato fuerte.',
                'priority' => '2',
            ],
            [
                'name' => 'Postres',
                'slug' => 'postres',
                'description' => 'Deliciosos dulces para cerrar la comida.',
                'priority' => '3',
            ],
            [
                'name' => 'Bebidas',
                'slug' => 'bebidas',
                'description' => 'Refrescos, jugos, gaseosas y bebidas calientes.',
                'priority' => '4',
            ],
        ];

        foreach ($categories as $category) {
            $category = Category::create($category);
            Menu::factory(rand(1, 3))->create(['category_id' => $category->id]);
        }
        #endregion

        #region States
        $states = [
            [
                'name' => 'Pago Pendiente',
                'slug' => 'pago-pendiente',
                'description' => 'Su orden está pendiente y aún no se ha realizado el pago.',
                'color' => '#ffb822'
            ],
            [
                'name' => 'Pago Confirmado',
                'slug' => 'pago-confirmado',
                'description' => 'El pago ha sido realizado y su orden está en cola para la cocina.',
                'color' => '#ff9f00'
            ],
            [
                'name' => 'En Preparación',
                'slug' => 'en-preparacion',
                'description' => 'Su orden está en la cocina y en proceso de preparación.',
                'color' => '#00b0f0'
            ],
            [
                'name' => 'En Entrega',
                'slug' => 'en-entrega',
                'description' => 'Su orden está en proceso de entrega y llegará pronto.',
                'color' => '#28a745'
            ],
            [
                'name' => 'Orden Cancelada',
                'slug' => 'orden-cancelada',
                'description' => 'La orden ha sido cancelada por el cliente o por un error en el sistema.',
                'color' => '#dc3545'
            ],
            [
                'name' => 'Orden Completada',
                'slug' => 'orden-completada',
                'description' => 'La orden ha sido entregada y el proceso se ha completado exitosamente.',
                'color' => '#17a2b8'
            ],
        ];

        foreach ($states as $state)
            State::create($state);
        #endregion

        #region Tables
        for ($i = 1; $i <= 10; $i++)
            Table::factory()->create(['number' => $i]);
        #endregion

        #region Roles
        $sa = Role::create(['name' => 'Super admin']);
        $r0 = Role::create(['name' => 'Administrador']);
        $r1 = Role::create(['name' => 'Empleado Cocinero/a']);
        $r2 = Role::create(['name' => 'Empleado Ayudante de cocina']);
        $r3 = Role::create(['name' => 'Empleado Cajero/a']);
        $r4 = Role::create(['name' => 'Empleado Mesero/a']);
        $r5 = Role::create(['name' => 'Cliente']);
        #endregion

        #region Permissions
        $tables = ['customer', 'employee', 'category', 'menu', 'state', 'user', 'table', 'role', 'permission'];
        foreach ($tables as $key => $value) {
            $p1 = Permission::factory()->create(['name' => $value . ':index',  'type' => 'api']);
            $p2 = Permission::factory()->create(['name' => $value . ':show',   'type' => 'api']);
            $p3 = Permission::factory()->create(['name' => $value . ':create', 'type' => 'api']);
            $p4 = Permission::factory()->create(['name' => $value . ':update', 'type' => 'api']);
            $p5 = Permission::factory()->create(['name' => $value . ':delete', 'type' => 'api']);
            $sa->givePermissionTo($p1);
            $sa->givePermissionTo($p2);
            $sa->givePermissionTo($p3);
            $sa->givePermissionTo($p4);
            $sa->givePermissionTo($p5);
        }
        #endregion

        #region Staff Admin - Start
        Person::factory(1)
            ->hasEmployee(['type' => 'AD'])
            ->hasUser(['role_id' => $r0->id])
            ->hasAddresses(1)
            ->create();
        #endregion

        #region Staff Cook - Start
        /*** whit user ***/
        Person::factory(1)
            ->hasEmployee(['type' => 'CO'])
            ->hasUser(['role_id' => $r1->id])
            ->hasAddresses(2)
            ->create();
        Person::factory(1)
            ->hasEmployee(['type' => 'CO'])
            ->hasUser(['role_id' => $r2->id])
            ->hasAddresses(2)
            ->create();
        /*** without user ***/
        Person::factory(2)
            ->hasEmployee(['type' => 'CO'])
            ->hasAddresses(2)
            ->create();
        #endregion

        #region Staff Cashier - Start
        Person::factory(1)
            ->hasEmployee(['type' => 'CA'])
            ->hasUser(['role_id' => $r3->id])
            ->hasAddresses(3)
            ->create();
        #endregion

        #region Staff Waiter - Start
        Person::factory(1)
            ->hasEmployee(['type' => 'WA'])
            ->hasUser(['role_id' => $r4->id])
            ->hasAddresses(2)
            ->create();
        Person::factory(2)
            ->hasEmployee(['type' => 'WA'])
            ->hasUser(['role_id' => $r4->id])
            ->hasAddresses(1)
            ->create();
        #endregion

        #region Customers - Start
        /*** whit user ***/
        Person::factory(12)
            ->hasCustomer()
            ->hasUser(['role_id' => $r5->id])
            ->hasAddresses(1)
            ->create();

        Person::factory(6)
            ->hasCustomer()
            ->hasUser(['role_id' => $r5->id])
            ->hasAddresses(3)
            ->create();
        /*** without user ***/
        Person::factory(4)
            ->hasCustomer()
            ->hasAddresses(1)
            ->create();
        #endregion

        $person1 = Person::create([
            'paternal_surname' => 'Admin',
            'maternal_surname' => 'Super',
            'names' => 'Super Admin',
            'gender' => 'M',
            'phone' => null
        ]);

        $employee = Employee::create(['type' => 'AD', 'person_id' => $person1->id]);

        $user = User::factory()->create(
            [
                'username' => 'userSuperAdmin',
                'email' => 'superadmin@gmail.com',
                'role_id' => $sa->id,
                'person_id' => $employee->person_id,
            ]
        );

        $user->save();

        #region Orders
        $stateIds = State::get()->pluck('id');
        $employeeWaiterIds = Employee::where('type', 'WA')->get()->pluck('person_id');
        foreach (Customer::all() as $customer) {
            Order::factory(rand(0, 3))
                ->create([
                    'customer_id' => $customer->person_id,
                    'employee_id' => $employeeWaiterIds->random(),
                    'state_id' => $stateIds->random()
                ]);
        }
        #endregion
    }
}
