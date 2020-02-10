let BookReducer = function (state, action) {
    switch (action.type) {
        //LIST ACTIONS
        case 'CURRENT-PAGE-SELECTED': {
            return Object.assign({}, state, {
                currentPage: action.payload
            });
        }
        case 'CURRENT-PAGE-RESET': {
            return Object.assign({}, state, {
                currentPage: 0
            });
        }

        //EDIT ACTIONS
        case 'SENDING-EDIT-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestEditFailed: false,
                sendingEditBookRequest: true
            });
        }
        case 'BOOK-EDIT-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                sendingEditBookRequest: false,
                requestEditFailed: false
            });
        }
        case 'BOOK-EDIT-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingEditBookRequest: false,
                requestEditFailed: true
            });
        }
        case 'BOOK-EDIT-REQUEST-ERRORS': {
            return Object.assign({}, state, {
                sendingEditBookRequest: false,
                requestEditErrors: action.payload.errors,
                requestEditFailedModel: action.payload.failedModel
            });
        }
        case 'BOOK-EDIT-RESET': {
            return Object.assign({}, state, {
                sendingEditBookRequest: false,
                requestEditFailed: false,
                requestEditErrors: null,
                requestEditFailedModel: null
            });
        }


        //CREATE ACTIONS
        case 'SENDING-CREATE-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestCreateFailed: false,
                sendingCreateBookRequest: true
            });
        }
        case 'BOOK-CREATE-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                sendingCreateBookRequest: false,
                requestCreateFailed: false
            });
        }
        case 'BOOK-CREATE-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingCreateBookRequest: false,
                requestCreateFailed: true
            });
        }
        case 'BOOK-CREATE-REQUEST-ERRORS': {
            return Object.assign({}, state, {
                sendingCreateBookRequest: false,
                requestCreateErrors: action.payload
            });
        }
        case 'BOOK-CREATE-RESET': {
            return Object.assign({}, state, {
                requestCreateFailed: false,
                sendingCreateBookRequest: false,
                requestCreateErrors: null
            });
        }

        //DELETE ACTIONS
        case 'SENDING-DELETE-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestDeleteFailed: false,
                sendingDeleteBookRequest: true
            });
        }
        case 'BOOK-DELETE-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                sendingDeleteBookRequest: false,
                requestDeleteFailed: false,
                requestDeleteSuccess: true
            });
        }
        case 'BOOK-DELETE-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingDeleteBookRequest: false,
                requestDeleteFailed: true
            });
        }
        case 'BOOK-DELETE-RESET': {
            return Object.assign({}, state, {
                sendingDeleteBookRequest: false,
                requestDeleteFailed: false,
                requestDeleteSuccess: false
            });
        }

        //FETCH ACTIONS
        case 'SENDING-FETCH-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestFetchFailed: false,
                sendingFetchBookRequest: true
            });
        }
        case 'BOOK-FETCH-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                books: action.payload.books,
                pageCount: action.payload.pageCount,
                perPage: action.payload.perPage,
                sendingFetchBookRequest: false,
                requestFetchFailed: false,
                requestFetchSuccess: true
            });
        }
        case 'BOOK-FETCH-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingFetchBookRequest: false,
                requestFetchFailed: true
            });
        }

        //FETCH EDIT ACTIONS
        case 'SENDING-EDIT-FETCH-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestEditFetchFailed: false,
                sendingEditFetchBookRequest: true
            });
        }
        case 'BOOK-EDIT-FETCH-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                editBook: action.payload.book,
                authors: action.payload.authors,
                genres: action.payload.genres,
                sendingEditFetchBookRequest: false,
                requestEditFetchFailed: false,
            });
        }
        case 'BOOK-EDIT-FETCH-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingEditFetchBookRequest: false,
                requestEditFetchFailed: true
            });
        }

        //FETCH CREATE ACTIONS
        case 'SENDING-CREATE-FETCH-BOOK-REQUEST': {
            return Object.assign({}, state, {
                requestCreateFetchFailed: false,
                sendingCreateFetchBookRequest: true
            });
        }
        case 'BOOK-CREATE-FETCH-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                authors: action.payload.authors,
                genres: action.payload.genres,
                sendingCreateFetchBookRequest: false,
                requestCreateFetchFailed: false,
            });
        }
        case 'BOOK-CREATE-FETCH-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingCreateFetchBookRequest: false,
                requestCreateFetchFailed: true
            });
        }

        //DEFAULT ACTION
        default: {
            return Object.assign({}, state, {
                sendingFetchBookRequest: false,
                requestFetchFailed: false,
                requestFetchSuccess: false,
                currentPage: 0,
                perPage: 0,
                pageCount: 0
            });
        }
    }
};

export {BookReducer};
