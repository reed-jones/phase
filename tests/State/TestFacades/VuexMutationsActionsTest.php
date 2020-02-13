<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;

class VuexMutationsActionsTest extends TestCase
{
    public function test_commit_mutations_output_correctly()
    {
        Vuex::commit('SET_COUNT', 5);

        $this->assertVuex([
            'mutations' => [['SET_COUNT', 5]],
        ]);
    }

    public function test_commit_mutations_without_value_output_correctly()
    {
        Vuex::commit('INCREMENT');

        $this->assertVuex([
            'mutations' => [['INCREMENT']],
        ]);
    }

    public function test_commit_mutations_output_multiple_correctly()
    {
        $number = 5;
        $user = [
            'email' => 'reed@example.com',
            'name' => 'Reed Jones',
        ];
        Vuex::commit('SET_COUNT', $number);
        Vuex::commit('users/SET_USER', $user);

        $this->assertVuex([
            'mutations' => [
                ['SET_COUNT', $number],
                ['users/SET_USER', $user],
            ],
        ]);
    }

    public function test_dispatch_actions_output_correctly()
    {
        Vuex::dispatch('setCount', 5);

        $this->assertVuex([
            'actions' => [['setCount', 5]],
        ]);
    }

    public function test_dispatch_actions_without_value_output_correctly()
    {
        Vuex::dispatch('increment');

        $this->assertVuex([
            'actions' => [['increment']],
        ]);
    }

    public function test_dispatch_actions_output_multiple_correctly()
    {
        $number = 5;
        $user = [
            'email' => 'reed@example.com',
            'name' => 'Reed Jones',
        ];

        Vuex::dispatch('setCount', $number);
        Vuex::dispatch('increment');
        Vuex::dispatch('users/setUser', $user);

        $this->assertVuex([
            'actions' => [
                ['setCount', $number],
                ['increment'],
                ['users/setUser', $user],
            ],
        ]);
    }
}
