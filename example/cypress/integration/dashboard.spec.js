describe('Dashboard', () => {
    it('should not allow guests to view the dashboard', () => {
        cy.visit('/home')
            .url().should('include', '/login')
    })

    it('allows logged in users to see their dashboard', () => {
        cy.login().should(user => {
            cy.visit('/home')
                .contains(`Welcome ${user.name}`)
        })
    })

    it("increments & decrements the counter", () => {
        cy.login()
        cy.visit('/home')
        // check counter
        cy.get('[data-cy=counter-count]').contains(0)
        cy.get('[data-cy=counter-inc]').click()
        cy.get('[data-cy=counter-count]').contains(1)
        cy.get('[data-cy=counter-inc]').click()
        cy.get('[data-cy=counter-count]').contains(2)

        cy.get('[data-cy=counter-dec]').click()
        cy.get('[data-cy=counter-count]').contains(1)
    })
})
