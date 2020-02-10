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

//EDIT ACTIONS
export function sendingEditBookRequest() {
    return {
        type: 'SENDING-EDIT-BOOK-REQUEST',
    }
}

export function bookEditRequestFinished(books) {
    return {
        type: 'BOOK-EDIT-REQUEST-FINISHED',
        payload: books
    }
}

export function bookEditRequestErrors(errors, failedModel) {
    return {
        type: 'BOOK-EDIT-REQUEST-ERRORS',
        payload: {
            'errors' : errors,
            'failedModel' : failedModel
        }
    }
}

export function bookEditRequestFailed() {
    return {
        type: 'BOOK-EDIT-REQUEST-FAILED',
    }
}

export function bookEditReset() {
    return {
        type: 'BOOK-EDIT-RESET',
    }
}

//CREATE ACTIONS
export function sendingCreateBookRequest() {
    return {
        type: 'SENDING-EDIT-BOOK-REQUEST',
    }
}

export function bookCreateRequestFinished(books) {
    return {
        type: 'BOOK-CREATE-REQUEST-FINISHED',
        payload: books
    }
}

export function bookCreateRequestErrors(errors) {
    return {
        type: 'BOOK-CREATE-REQUEST-ERRORS',
        payload: errors
    }
}

export function bookCreateRequestFailed() {
    return {
        type: 'BOOK-CREATE-REQUEST-FAILED',
    }
}

export function bookCreateReset() {
    return {
        type: 'BOOK-CREATE-RESET',
    }
}

//DELETE ACTIONS
export function sendingDeleteBookRequest() {
    return {
        type: 'SENDING-DELETE-BOOK-REQUEST',
    }
}

export function bookDeleteRequestFinished(books) {
    return {
        type: 'BOOK-DELETE-REQUEST-FINISHED',
        payload: books
    }
}

export function bookDeleteRequestFailed() {
    return {
        type: 'BOOK-DELETE-REQUEST-FAILED',
    }
}

export function bookDeleteReset() {
    return {
        type: 'BOOK-DELETE-RESET',
    }
}

//FETCH ACTIONS
export function sendingFetchBookRequest() {
    return {
        type: 'SENDING-FETCH-BOOK-REQUEST',
    }
}

export function bookFetchRequestFinished(books, pageCount, perPage) {
    return {
        type: 'BOOK-FETCH-REQUEST-FINISHED',
        payload: {
            'books' : books,
            'pageCount' : pageCount,
            'perPage' : perPage
        }
    }
}

export function bookFetchRequestFailed() {
    return {
        type: 'BOOK-FETCH-REQUEST-FAILED',
    }
}

//EDIT FETCH ACTIONS
export function sendingEditFetchBookRequest() {
    return {
        type: 'SENDING-EDIT-FETCH-BOOK-REQUEST',
    }
}

export function bookEditFetchRequestFinished(book, authors, genres) {
    return {
        type: 'BOOK-EDIT-FETCH-REQUEST-FINISHED',
        payload: {
            'book': book,
            'authors': authors,
            'genres': genres
        }
    }
}

export function bookEditFetchRequestFailed() {
    return {
        type: 'BOOK-EDIT-FETCH-REQUEST-FAILED',
    }
}

//CREATE FETCH ACTIONS
export function sendingCreateFetchBookRequest() {
    return {
        type: 'SENDING-CREATE-FETCH-BOOK-REQUEST',
    }
}

export function bookCreateFetchRequestFinished(authors, genres) {
    return {
        type: 'BOOK-CREATE-FETCH-REQUEST-FINISHED',
        payload: {
            'authors': authors,
            'genres': genres
        }
    }
}

export function bookCreateFetchRequestFailed() {
    return {
        type: 'BOOK-CREATE-FETCH-REQUEST-FAILED',
    }
}


