declare namespace App.DTOs {
    export type TEloChange = {
        id: number;
        game_id: number;
        player_id: number;
        change: number;
    };
    export type TGame = {
        id: number;
        first_team_id: number;
        second_team_id: number | null;
        date: string;
        location_id: number;
        first_team: App.DTOs.TTeam;
        second_team: App.DTOs.TTeam | null;
        result: App.DTOs.TGameResult | null;
        winning_team: App.DTOs.TTeam | null;
        price_in_cent: number;
        elo_changes: Array<App.DTOs.TEloChange>;
        payments: Array<App.DTOs.TGamePayment>;
    };
    export type TGamePayment = {
        id: number;
        game_id: number;
        player_id: number;
        amount_in_cent: number;
        paypal_payment_id: string | null;
        status: string;
        player: App.DTOs.TPlayer;
        payer: App.DTOs.TUser | null;
    };
    export type TGameResult = {
        id: number;
        game_id: number;
        sets: { [key: number]: App.DTOs.TSet };
    };
    export type TLocation = {
        id: number;
        name: string;
    };
    export type TPlayer = {
        id: number;
        first_name: string;
        last_name: string;
        elo: number;
        user: App.DTOs.TUser | null;
    };
    export type TSet = {
        first_team: number;
        second_team: number;
    };
    export type TTeam = {
        id: number;
        players: Array<App.DTOs.TPlayer>;
    };
    export type TUser = {
        id: number;
        email: string | null;
    };
}
