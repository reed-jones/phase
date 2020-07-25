describe('Dashboard', () => {
    it('should not allow guests to view the dashboard', () => {
        cy.visit('/home')
            .url().should('include', '/login')
    })

    it('allows logged in users to see their dashboard', () => {
        cy.login().then(user => {
            cy.visit('/home')
                .contains("Welcome to: HomeController@DashboardPage")
        })
    })
})
