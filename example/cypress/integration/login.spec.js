describe('Login', () => {

    beforeEach(() => {
        // cy.exec('php artisan migrate:fresh --seed --env=cypress')
        cy.artisan('migrate:fresh --seed')
    })

    it('shows the login page', () => {
        cy.visit('/login').contains('Welcome Back')
    })

    it('displays an error for invalid login credentials', () => {
        cy.visit('/login')

        cy.get('#email').type('foo@bar.com')
        cy.get('#current-password').type('wrong')
        cy.get('button').contains('Sign in').click()
        cy.contains('These credentials do not match our records.')
    })

    it('redirects to the home page after a user logs in', () => {
        cy.create('App\\Models\\User').then(user => {
            cy.visit('/login')

            cy.get('#email').type(user.email);
            cy.get('#current-password').type('password')
            cy.get('button').contains('Sign in').click()

            cy.url().should('include', '/home');
            cy.contains("Welcome to: HomeController@DashboardPage")
        })
    })
})
