describe("Notices", () => {
    beforeEach(() => cy.artisan('migrate:fresh --seed'))

    it('shows the notices page', () => {
        cy.visit('/')
        cy.contains('Notices')
        cy.contains('Page: 1')
    })

    it("loads the page without javascript", () => {
        cy.visit("/", { script: false })
        cy.contains('Notices')
        cy.contains('Page: 1')
    })

    it("navigates to the next page & previous pages", () => {
        cy.visit("/")
        cy.contains('Page: 1')
        cy.get('[data-cy=nav-next]').contains('Next').click()
        cy.contains('Page: 2')
        cy.get('[data-cy=nav-previous]').contains('Previous').click()
        cy.contains('Page: 1')
    })

    it("navigates to the next page & previous pages without javascript", () => {
        cy.visit("/", { script: false })
        cy.contains('Page: 1')
        cy.get('[data-cy=nav-next]').contains('Next').click()
        cy.contains('Page: 2')
        cy.get('[data-cy=nav-previous]').contains('Previous').click()
        cy.contains('Page: 1')
    })

    it("does not show contact information when not logged in", () => {
        cy.visit('/')
        cy.get('[data-cy=contact-email]').should('not.exist')
    })

    it("does show contact information when logged in", () => {
        cy.login().then(() => {
            cy.visit('/')
            cy.get('[data-cy=contact-email]').should('exist')
        })
    })
})
