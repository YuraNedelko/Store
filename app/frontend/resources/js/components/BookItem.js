import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from 'react-redux';
import {
    sendingViewFetchRequest,
    viewFetchRequestFinished,
    viewFetchRequestFailed,
} from '../actions/BookActions'

class BookItem extends Component {
    constructor(props) {
        super(props);
        this.view = this.viewBook.bind(this);
    }

    fetchBook() {
        this.props.dispatch(sendingViewFetchRequest());
        fetch(`${address}/home/books/view/${this.props.book.id}`, {
            method: 'GET',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        this.props.dispatch(viewFetchRequestFinished(response.book, response.authors, response.genres));
                    }
                }
            )
            .catch(() => this.props.dispatch(viewFetchRequestFailed()));
    }



    viewBook() {
        this.fetchBook();
    }


    render() {
        return (
            <div className="row book-item">
                <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                    <span> {this.props.book.name} </span>
                </div>

                <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                    <span>{this.props.book.price}</span>
                </div>

                <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                    <span className="book-description">{this.props.book.short_description} </span>
                </div>

                <Link onClick={this.view} className="col-lg-2 col-md-2 col-sm-2 book-table-cell"
                      to={`/book/view/${this.props.book.id}`}>
                    <button type="button">
                        View
                    </button>
                </Link>
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        pageCount: state.pageCount,
        perPage: state.perPage,
        currentPage: state.currentPage
    };
};

export default connect(mapStateToProps)(BookItem);