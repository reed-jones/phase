describe('Dashboard', () => {
    it.only('should redirect guests away from protected routes on direct entry', () => {
        cy.visit('/home')
            .url().should('include', '/login')
    })
    it.only('should redirect guests away from protected routes on SPA entry', () => {
        cy.visit('/')
        cy.get('[data-cy=navbar-dashboard]').click()
        cy.get('[data-cy=navbar-dashboard]').click()
        cy.get('[data-cy=navbar-dashboard]').click()
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
