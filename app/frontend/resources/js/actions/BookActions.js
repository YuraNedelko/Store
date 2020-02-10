//BOOK LIST ACTIONS
export function currentPageSelected(selectedPage) {
    return {
        type: 'CURRENT-PAGE-SELECTED',
        payload: selectedPage
    }
}

export function currentPageReset() {
    return {
        type: 'CURRENT-PAGE-RESET',
    }
}


//FETCH ACTIONS
export function sendingFetchBookRequest() {
    return {
        type: 'SENDING-FETCH-BOOK-REQUEST',
    }
}

export function bookFetchRequestFinished(books, pageCount, perPage, authors, genres) {
    return {
        type: 'BOOK-FETCH-REQUEST-FINISHED',
        payload: {
            'books': books,
            'pageCount': pageCount,
            'perPage': perPage,
            'authors': authors,
            'genres': genres
        }
    }
}

export function bookFetchRequestFailed() {
    return {
        type: 'BOOK-FETCH-REQUEST-FAILED',
    }
}

//VIEW FETCH ACTIONS
export function sendingViewFetchRequest() {
    return {
        type: 'SENDING-VIEW-FETCH-REQUEST',
    }
}

export function viewFetchRequestFinished(book, authors, genres) {
    return {
        type: 'VIEW-FETCH-REQUEST-FINISHED',
        payload: {
            'book': book,
            'authors': authors,
            'genres': genres
        }
    }
}

export function viewFetchRequestFailed() {
    return {
        type: 'VIEW-FETCH-REQUEST-FAILED',
    }
}

//VIEW FORM ACTIONS
export function showViewForm() {
    return {
        type: 'SHOW-VIEW-FORM',
    }
}

export function resetViewFormVisibility() {
    return{
        type: 'RESET-VIEW-FORM-VISIBILITY',
    }
}

export function sendingViewFormRequest() {
    return {
        type: 'SENDING-VIEW-FORM-REQUEST',
    }
}

export function resetViewForm() {
    return {
        type: 'RESET-VIEW-FORM',
    }
}

export function viewFormErrors(errors) {
    return {
        type: 'VIEW-FORM-ERRORS',
        payload: errors
    }
}

export function viewFormSuccess() {
    return {
        type: 'VIEW-FORM-SUCCESS',
    }
}

export function viewFormFailed() {
    return {
        type: 'VIEW-FORM-FAILED',
    }
}

//SELECTED AUTHOR ACTIONS
export function selectedAuthorChanged(selectedAuthor) {
    return {
        type: 'SELECTED-AUTHOR-CHANGED',
        payload: selectedAuthor
    }
}

//SELECTED GENRE ACTIONS
export function selectedGenreChanged(selectedGenre) {
    return {
        type: 'SELECTED-GENRE-CHANGED',
        payload: selectedGenre
    }
}

