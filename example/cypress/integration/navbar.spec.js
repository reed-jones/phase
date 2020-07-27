describe("Navbar", () => {
    it("Shows a login link on the home page when not logged in", () => {
        cy.visit('/')
        cy.get('[data-cy=navbar-logout]').should('not.exist')
        cy.get('[data-cy=navbar-login]').click()
        cy.url().should('be', '/login')
        cy.get('[data-cy=navbar-home]').should('exist')
        cy.get('[data-cy=navbar-login]').should('not.exist')
        cy.get('[data-cy=navbar-logout]').should('not.exist')
    })

    it("Shows a logout link on the home page when logged in", () => {
        cy.visit('/')
        cy.get('[data-cy=navbar-login]').should('not.exist')
        cy.get('[data-cy=navbar-logout]').click()

        // stay on the same page, however now are logged out
        cy.url().should('be', '/')
        cy.get('[data-cy=navbar-logout]').should('not.exist')
        cy.get('[data-cy=navbar-login]').should('exist')
    })
})
