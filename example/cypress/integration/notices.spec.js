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
        cy.get('button').contains('Next').click()
        cy.contains('Page: 2')
        cy.get('button').contains('Previous').click()
        cy.contains('Page: 1')
    })
})
