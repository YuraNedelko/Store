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
                authors: action.payload.authors,
                genres: action.payload.genres,
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

        //FETCH VIEW ACTIONS
        case 'SENDING-VIEW-FETCH-REQUEST': {
            return Object.assign({}, state, {
                requestViewFetchFailed: false,
                sendingViewFetchRequest: true
            });
        }
        case 'VIEW-FETCH-REQUEST-FINISHED': {
            return Object.assign({}, state, {
                viewBook: action.payload.book,
                sendingViewFetchRequest: false,
                requestViewFetchFailed: false,
            });
        }
        case 'VIEW-FETCH-REQUEST-FAILED': {
            return Object.assign({}, state, {
                sendingViewFetchRequest: false,
                requestViewFetchFailed: true
            });
        }

        //VIEW FORM ACTIONS
        case 'SHOW-VIEW-FORM': {
            return Object.assign({}, state, {
                viewFormShow: true
            });
        }
        case 'RESET-VIEW-FORM-VISIBILITY': {
            return Object.assign({}, state, {
                viewFormShow: false,
                viewFormSuccess: false
            });
        }
        case 'SENDING-VIEW-FORM-REQUEST': {
            return Object.assign({}, state, {
                sendingViewFormRequest: true,
                viewFormFailed: false,
                viewFormSuccess: false
            });
        }
        case 'RESET-VIEW-FORM': {
            return Object.assign({}, state, {
                viewFormErrors: false,
                sendingViewFormRequest: false,
                viewFormFailed: false,
                viewFormSuccess: false
            });
        }
        case 'VIEW-FORM-ERRORS': {
            return Object.assign({}, state, {
                viewFormErrors: action.payload,
                sendingViewFormRequest: false,
                viewFormSuccess: false
            });
        }
        case 'VIEW-FORM-SUCCESS': {
            return Object.assign({}, state, {
                sendingViewFormRequest: false,
                viewFormSuccess: true,
                viewFormFailed: false,
                viewFormShow: false
            });
        }
        case 'VIEW-FORM-FAILED': {
            return Object.assign({}, state, {
                viewFormFailed: true,
                viewFormSuccess: false
            });
        }

        //AUTHOR CHANGE ACTIONS
        case 'SELECTED-AUTHOR-CHANGED': {
            return Object.assign({}, state, {
                selectedAuthor: action.payload
            });
        }

        //GENRE CHANGE ACTIONS
        case 'SELECTED-GENRE-CHANGED': {
            return Object.assign({}, state, {
                selectedGenre: action.payload
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
                pageCount: 0,
                selectedAuthor: null,
                selectedGenre: null
            });
        }
    }
};

export {BookReducer};
